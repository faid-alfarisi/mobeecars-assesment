import AsyncStorage from '@react-native-async-storage/async-storage';
import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';

export const TOKEN_KEY = 'egrade.access_token';
export const USER_KEY = 'egrade.user';

const baseURL = process.env.EXPO_PUBLIC_API_URL ?? 'http://18.143.166.160/api';

// Visible in the Metro / console log so you can confirm the app picked up the right URL.
// If this is wrong after editing .env, restart metro with `npx expo start --clear`.
console.log('[api] baseURL =', baseURL);

export const api = axios.create({
  baseURL,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  timeout: 15000,
});

api.interceptors.request.use(async (config: InternalAxiosRequestConfig) => {
  const token = await AsyncStorage.getItem(TOKEN_KEY);
  if (token) {
    config.headers.set('Authorization', `Bearer ${token}`);
  }
  return config;
});

let onUnauthorized: (() => void) | null = null;

export function setUnauthorizedHandler(handler: () => void) {
  onUnauthorized = handler;
}

api.interceptors.response.use(
  (response) => response,
  async (error: AxiosError) => {
    if (error.response?.status === 401) {
      await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
      onUnauthorized?.();
    }
    return Promise.reject(error);
  },
);
