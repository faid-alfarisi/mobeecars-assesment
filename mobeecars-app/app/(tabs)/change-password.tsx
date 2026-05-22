import { useState } from 'react';
import {
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  Text,
  TextInput,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { api } from '@/lib/api';

type ApiResponse = {
  status?: string;
  message?: string;
  errors?: unknown;
};

function flattenErrors(errors: unknown): string | null {
  if (!errors) return null;
  if (typeof errors === 'string') return errors;
  if (Array.isArray(errors)) return errors.join('\n');
  if (typeof errors === 'object') {
    const parts: string[] = [];
    for (const value of Object.values(errors as Record<string, unknown>)) {
      if (Array.isArray(value)) parts.push(...value.map(String));
      else if (typeof value === 'string') parts.push(value);
    }
    return parts.length > 0 ? parts.join('\n') : null;
  }
  return null;
}

export default function ChangePassword() {
  const router = useRouter();
  const [oldPassword, setOldPassword] = useState('');
  const [password, setPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [success, setSuccess] = useState<string | null>(null);

  const onSubmit = async () => {
    setError(null);
    setSuccess(null);

    if (!oldPassword || !password || !confirmPassword) {
      setError('All fields are required.');
      return;
    }
    if (password !== confirmPassword) {
      setError('New password and confirmation do not match.');
      return;
    }

    setSubmitting(true);
    try {
      const res = await api.post<ApiResponse>('/change-password', {
        old_password: oldPassword,
        password,
        confirm_password: confirmPassword,
      });
      if (res.data?.status === 'error') {
        setError(
          flattenErrors(res.data.errors) ?? 'Could not change password.',
        );
      } else {
        setSuccess(res.data?.message ?? 'Password updated successfully.');
        setOldPassword('');
        setPassword('');
        setConfirmPassword('');
        setTimeout(() => router.push('./profile'), 800);
      }
    } catch (err) {
      const message =
        (err as { response?: { data?: ApiResponse } }).response?.data
          ?.message ?? 'Could not reach the server.';
      setError(message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView edges={['top']} className="flex-1 bg-slate-50">
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        className="flex-1"
      >
        <ScrollView contentContainerClassName="p-5 gap-5">
          <View>
            <Text className="text-2xl font-bold text-slate-900">
              Change Password
            </Text>
            <Text className="mt-1 text-sm text-slate-500">
              Use a strong password you don't reuse elsewhere.
            </Text>
          </View>

          <View className="gap-4 rounded-2xl border border-slate-200 bg-white p-5">
            <View>
              <Text className="mb-1 text-sm font-medium text-slate-700">
                Current password
              </Text>
              <TextInput
                value={oldPassword}
                onChangeText={setOldPassword}
                secureTextEntry
                autoCapitalize="none"
                placeholder="••••••••"
                placeholderTextColor="#94a3b8"
                className="rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-900"
              />
            </View>

            <View>
              <Text className="mb-1 text-sm font-medium text-slate-700">
                New password
              </Text>
              <TextInput
                value={password}
                onChangeText={setPassword}
                secureTextEntry
                autoCapitalize="none"
                placeholder="New password"
                placeholderTextColor="#94a3b8"
                className="rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-900"
              />
            </View>

            <View>
              <Text className="mb-1 text-sm font-medium text-slate-700">
                Confirm new password
              </Text>
              <TextInput
                value={confirmPassword}
                onChangeText={setConfirmPassword}
                secureTextEntry
                autoCapitalize="none"
                placeholder="Re-enter new password"
                placeholderTextColor="#94a3b8"
                className="rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-900"
              />
            </View>

            {error ? (
              <Text className="text-sm text-red-600">{error}</Text>
            ) : null}
            {success ? (
              <Text className="text-sm text-emerald-600">{success}</Text>
            ) : null}

            <View className="flex-row gap-3 mt-2">
              <Pressable
                onPress={onSubmit}
                disabled={submitting}
                className="flex-1 items-center justify-center rounded-lg bg-brand-600 px-4 py-3 active:bg-brand-700 disabled:opacity-60"
              >
                {submitting ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <Text className="text-base font-semibold text-white">
                    Update password
                  </Text>
                )}
              </Pressable>
              <Pressable
                onPress={() => router.push('./profile')}
                disabled={submitting}
                className="flex-1 items-center justify-center rounded-lg bg-black px-4 py-3 active:bg-neutral-800 disabled:opacity-60"
              >
                {submitting ? (
                  <ActivityIndicator color="#fff" />
                ) : (
                  <Text className="text-base font-semibold text-white">
                    Cancel
                  </Text>
                )}
              </Pressable>
            </View>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}
