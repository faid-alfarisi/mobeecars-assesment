import { useEffect, useState, useCallback } from 'react';
import { ScrollView, Text, View } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { getMostLikedCar } from '@/database/preference';
import type { MostLikedCar } from '@/database/types';
import { useFocusEffect } from '@react-navigation/native';
import { api } from '@/lib/api';

function Row({
  label,
  value,
  border = true,
}: {
  label: string;
  value?: string | null;
  border?: boolean;
}) {
  return (
    <View
      className={`flex-row items-start justify-between ${border ? 'border-b border-slate-100' : ''} py-3 px-3`}
    >
      <Text className="text-sm text-slate-500">{label}</Text>
      <Text className="ml-4 flex-1 text-right text-sm font-medium text-slate-900">
        {value && String(value).length > 0 ? value : '—'}
      </Text>
    </View>
  );
}

interface ApiResponse {
  status?: string;
  errors?: string;
  data?: MostLikedCar;
}

export default function Report() {
  // local from sqlite
  const [yourBrand, setYourBrand] = useState<string>('Loading...');
  const [yourModel, setYourModel] = useState<string>('Loading...');
  const [yourType, setYourType] = useState<string>('Loading...');

  // global from api
  const [globalBrand, setGlobalBrand] = useState<string>('Loading...');
  const [globalModel, setGlobalModel] = useState<string>('Loading...');
  const [globalType, setGlobalType] = useState<string>('Loading...');

  useFocusEffect(
    useCallback(() => {
      loadReport();
    }, []),
  );

  const loadReport = async () => {
    try {
      const res: MostLikedCar | null = await getMostLikedCar();

      setYourBrand(res?.favorite_brand ?? 'No Data');
      setYourModel(res?.favorite_model ?? 'No Data');
      setYourType(res?.favorite_type ?? 'No Data');

      const resPrefs = await api.get<ApiResponse>('/global-preferences');
      let apiPrefs = (resPrefs.data?.data ?? {
        favorite_brand: null,
        favorite_model: null,
        favorite_type: null,
      }) as MostLikedCar;

      setGlobalBrand(apiPrefs?.favorite_brand ?? 'No Data');
      setGlobalModel(apiPrefs?.favorite_model ?? 'No Data');
      setGlobalType(apiPrefs?.favorite_type ?? 'No Data');
    } catch (err) {
      console.log(err);

      setYourBrand('No Data');
      setYourModel('No Data');
      setYourType('No Data');

      setGlobalBrand('No Data');
      setGlobalModel('No Data');
      setGlobalType('No Data');
    }
  };

  return (
    <SafeAreaView edges={['top']} className="flex-1 bg-slate-50">
      <ScrollView contentContainerClassName="p-5 gap-5">
        <View className="rounded-2xl border border-slate-200 bg-white">
          <Text className="border-b border-slate-200 px-5 py-4 text-center text-lg font-semibold">
            Your Most Liked Car
          </Text>

          <Row label="Brand" value={yourBrand} />
          <Row label="Model" value={yourModel} />
          <Row label="Type/Category" value={yourType} border={false} />
        </View>

        <View className="rounded-2xl border border-slate-200 bg-white">
          <Text className="border-b border-slate-200 px-5 py-4 text-center text-lg font-semibold">
            Global Most Liked Car
          </Text>

          <Row label="Brand" value={globalBrand} />
          <Row label="Model" value={globalModel} />
          <Row label="Type/Category" value={globalType} border={false} />
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}
