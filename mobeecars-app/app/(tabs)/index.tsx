import React, { useEffect, useState, useRef } from 'react';
import { ActivityIndicator, Image, Text, View } from 'react-native';
import Swiper from 'react-native-deck-swiper';
import { File, Paths } from 'expo-file-system';
import MaterialCommunityIcons from '@expo/vector-icons/MaterialCommunityIcons';
import { api } from '@/lib/api';
import { getCars, refreshCars } from '@/database/car';
import { getPreferences, savePreferences } from '@/database/preference';
import type { Car, UserPreference } from '@/database/types';

const SITE_URL = process.env.EXPO_PUBLIC_URL;

interface ApiResponse {
  status?: string;
  errors?: string;
  data?: Car[] | UserPreference[];
}

export default function HomeScreen() {
  const [cars, setCars] = useState<Car[]>([]);
  const [loading, setLoading] = useState(true);
  const prefsRef = useRef<UserPreference[]>([]);

  useEffect(() => {
    initCarsPrefs();
  }, []);

  const initCarsPrefs = async () => {
    try {
      // CARS
      const resCars = await api.get<ApiResponse>('/cars');
      let apiCars = (resCars.data?.data ?? []) as Car[];

      // Download images locally
      const processedCars = await Promise.all(
        apiCars.map(async (car) => {
          try {
            const imageUrl = `${SITE_URL}/storage/${car.image}`;
            const extension = car.image.split('.').pop() ?? 'png';
            const localPath = `${Paths.document.uri}cars_${car.id}.${extension}`;
            const file = new File(localPath);
            const info = file.info();

            // download only if not exists
            if (!info.exists) {
              await File.downloadFileAsync(imageUrl, file);
            }

            return {
              ...car,
              local_image: file.uri,
            };
          } catch (err) {
            console.log('download image error', err);
            return car;
          }
        }),
      );

      // refresh sqlite cache
      await refreshCars(processedCars);

      setCars(processedCars);

      // PREFERENCES
      const resPrefs = await api.get<ApiResponse>('/preferences');
      let apiPrefs = (resPrefs.data?.data ?? []) as UserPreference[];

      await savePreferences(apiPrefs);
    } catch (err) {
      console.log('offline mode / api failed', err);

      // offline fallback
      const offlineCars = await getCars();

      setCars(offlineCars);
    } finally {
      prefsRef.current = await getPreferences(0);
      setLoading(false);
    }
  };

  const swipeCard = async (car_id: number, liked: number) => {
    const newPref = { car_id, liked, synced: 0, synced_at: 0 };
    prefsRef.current.push(newPref);
    try {
      const res = await api.post<ApiResponse>('/save-preferences', {
        prefs: prefsRef.current,
      });
      if (res.data?.status === 'success') {
        await savePreferences(
          prefsRef.current.map((el) => {
            el.synced = 1;
            el.synced_at = Math.floor(Date.now() / 1000);
            return el;
          }),
        );
        prefsRef.current = [];
      } else {
        await savePreferences([newPref]);
      }
    } catch (err) {
      console.log(err);
      await savePreferences([newPref]);
    }
  };

  if (loading) {
    return (
      <View className="flex-1 items-center justify-center">
        <ActivityIndicator size="large" />
      </View>
    );
  }

  if (cars.length <= 0) {
    return (
      <View className="flex-1 items-center justify-center">
        <Text>No cars available</Text>
      </View>
    );
  }

  return (
    <View className="flex-1 justify-center bg-slate-50">
      <Swiper
        cards={cars}
        stackSize={3}
        backgroundColor="transparent"
        disableTopSwipe
        disableBottomSwipe
        animateCardOpacity
        animateOverlayLabelsOpacity
        onSwipedLeft={(cardIndex) => {
          console.log('left');
          swipeCard(cars[cardIndex].id, 0);
        }}
        onSwipedRight={(cardIndex) => {
          console.log('right');
          swipeCard(cars[cardIndex].id, 1);
        }}
        renderCard={(car: Car) => {
          if (!car) {
            return null;
          }

          return (
            <View
              className="self-center overflow-hidden rounded-2xl border border-slate-200 bg-white"
              style={{ height: '100%' }}
            >
              <Image
                source={{
                  uri: car.local_image ?? `${SITE_URL}/storage/${car.image}`,
                }}
                resizeMode="cover"
                className="w-full aspect-[4/3]"
              />

              <View className="flex-1 p-5">
                <Text className="text-base text-gray-500">{car.brand}</Text>

                <Text className="mt-1 text-3xl font-bold text-black">
                  {car.model}
                </Text>

                <View className="mt-4 self-start rounded-full bg-black px-4 py-2">
                  <Text className="font-semibold text-white">{car.type}</Text>
                </View>
              </View>

              <View className="mb-5 mx-3 flex-row items-center justify-between">
                <Text className="font-semibold text-lg text-red-500">
                  <MaterialCommunityIcons
                    name="chevron-double-left"
                    size={18}
                  />{' '}
                  Dislike
                </Text>
                <Text className="font-semibold text-lg text-green-600">
                  Like{' '}
                  <MaterialCommunityIcons
                    name="chevron-double-right"
                    size={18}
                  />
                </Text>
              </View>
            </View>
          );
        }}
      />
    </View>
  );
}
