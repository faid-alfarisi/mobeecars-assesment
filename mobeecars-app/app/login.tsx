import { Ionicons } from '@expo/vector-icons';
import { useState } from 'react';
import {
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  Text,
  TextInput,
  View,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useAuth } from '@/lib/auth';

export default function Login() {
  const { login } = useAuth();
  const [email, setEmail] = useState('user@user.com');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const onSubmit = async () => {
    if (!email.trim() || !password) {
      setError('Phone and password are required.');
      return;
    }
    setSubmitting(true);
    setError(null);
    const result = await login(email.trim(), password);
    setSubmitting(false);
    if (!result.ok) setError(result.message);
  };

  return (
    <SafeAreaView className="flex-1 bg-white">
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        className="flex-1"
      >
        <View className="flex-1 justify-center px-6">
          <Text className="text-3xl font-bold text-slate-900">Car Match</Text>
          <Text className="mt-1 text-base text-slate-500">
            Sign in to your account
          </Text>

          <View className="mt-8 gap-4">
            <View>
              <Text className="mb-1 text-sm font-medium text-slate-700">
                Email
              </Text>
              <TextInput
                value={email}
                onChangeText={setEmail}
                placeholder="e.g. user@user.com"
                placeholderTextColor="#94a3b8"
                autoCapitalize="none"
                autoCorrect={false}
                className="rounded-lg border border-slate-300 bg-white px-4 py-3 text-base text-slate-900"
              />
            </View>

            <View>
              <Text className="mb-1 text-sm font-medium text-slate-700">
                Password
              </Text>
              <View className="relative">
                <TextInput
                  value={password}
                  onChangeText={setPassword}
                  placeholder="Your password"
                  placeholderTextColor="#94a3b8"
                  secureTextEntry={!showPassword}
                  autoCapitalize="none"
                  autoCorrect={false}
                  className="rounded-lg border border-slate-300 bg-white pl-4 pr-12 py-3 text-base text-slate-900"
                />
                <Pressable
                  onPress={() => setShowPassword((v) => !v)}
                  hitSlop={8}
                  accessibilityRole="button"
                  accessibilityLabel={
                    showPassword ? 'Hide password' : 'Show password'
                  }
                  className="absolute right-3 top-0 bottom-0 justify-center px-1"
                >
                  <Ionicons
                    name={showPassword ? 'eye-off-outline' : 'eye-outline'}
                    size={22}
                    color="#64748b"
                  />
                </Pressable>
              </View>
            </View>

            {error ? (
              <Text className="text-sm text-red-600">{error}</Text>
            ) : null}

            <Pressable
              onPress={onSubmit}
              disabled={submitting}
              className="mt-2 items-center justify-center rounded-lg bg-brand-600 px-4 py-3 active:bg-brand-700 disabled:opacity-60"
            >
              {submitting ? (
                <ActivityIndicator color="#fff" />
              ) : (
                <Text className="text-base font-semibold text-white">
                  Sign in
                </Text>
              )}
            </Pressable>
          </View>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}
