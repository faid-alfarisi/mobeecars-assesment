import { useCallback } from 'react';
import {
  ActivityIndicator,
  Pressable,
  ScrollView,
  Text,
  View,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useFocusEffect, useRouter } from 'expo-router';
import { useAuth } from '@/lib/auth';
import moment from 'moment';
import { clearCars } from '@/database/car';
import { clearPreferences } from '@/database/preference';

function Row({ label, value }: { label: string; value?: string | null }) {
  return (
    <View className="flex-row items-start justify-between border-b border-slate-100 py-3">
      <Text className="text-sm text-slate-500">{label}</Text>
      <Text className="ml-4 flex-1 text-right text-sm font-medium text-slate-900">
        {value && String(value).length > 0 ? value : '-'}
      </Text>
    </View>
  );
}

export default function Profile() {
  const { user, refreshProfile, logout } = useAuth();
  const router = useRouter();

  useFocusEffect(
    useCallback(() => {
      refreshProfile();
    }, [refreshProfile]),
  );

  const doLogout = () => {
    Alert.alert('Log out', `Are you sure you want to log out?`, [
      {
        text: 'No',
        style: 'cancel',
      },
      {
        text: 'Yes',
        onPress: () => {
          logout();
          clearCars();
          clearPreferences();
        },
      },
    ]);
  };

  if (!user) {
    return (
      <View className="flex-1 items-center justify-center bg-slate-50">
        <ActivityIndicator size="large" color="#2563eb" />
      </View>
    );
  }

  const initial =
    (user.initial as string | undefined) ??
    (user.name ? user.name.trim().charAt(0).toUpperCase() : '?');

  return (
    <SafeAreaView edges={['top']} className="flex-1 bg-slate-50">
      <ScrollView contentContainerClassName="p-5 gap-5">
        <View className="items-center rounded-2xl border border-slate-200 bg-white p-6">
          <View className="h-20 w-20 items-center justify-center rounded-full bg-brand-100">
            <Text className="text-3xl font-bold text-brand-700">{initial}</Text>
          </View>
          <Text className="mt-4 text-xl font-semibold text-slate-900">
            {user.name}
          </Text>
          {user.designation ? (
            <Text className="mt-1 text-sm text-slate-500">
              {String(user.designation)}
            </Text>
          ) : null}
        </View>

        <View className="rounded-2xl border border-slate-200 bg-white px-5">
          <Row label="Email" value={user.email as string | undefined} />
          <Row
            label="Created Date"
            value={
              moment(user.created_at as string | undefined).format(
                'DD/MM/YYYY',
              ) as string | undefined
            }
          />
          <Row label="Role" value={user.role as string | undefined} />
        </View>

        <View className="gap-3">
          <Pressable
            onPress={() => router.push('./change-password')}
            className="items-center rounded-lg border border-brand-600 bg-white px-4 py-3 active:bg-brand-50"
          >
            <Text className="text-base font-semibold text-brand-700">
              Change Password
            </Text>
          </Pressable>

          <Pressable
            onPress={doLogout}
            className="items-center rounded-lg bg-red-600 px-4 py-3 active:bg-red-700"
          >
            <Text className="text-base font-semibold text-white">Log out</Text>
          </Pressable>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}
