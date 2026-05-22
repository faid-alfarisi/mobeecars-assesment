import AsyncStorage from '@react-native-async-storage/async-storage';
import React, {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useMemo,
    useState,
} from 'react';
import { api, setUnauthorizedHandler, TOKEN_KEY, USER_KEY } from './api';

export type User = {
  id: number;
  name: string;
  code?: string | null;
  phone?: string | null;
  type?: string | null;
  designation?: string | null;
  employment_type?: string | null;
  initial?: string | null;
  [key: string]: unknown;
};

export type CurrentProject = {
  id: number;
  code?: string | null;
  name?: string | null;
  address?: string | null;
  effective_from?: string | null;
  effective_to?: string | null;
};

type LoginResult = { ok: true } | { ok: false; message: string };

type AuthContextValue = {
  user: User | null;
  token: string | null;
  currentProject: CurrentProject | null;
  isLoading: boolean;
  login: (phone: string, password: string) => Promise<LoginResult>;
  logout: () => Promise<void>;
  refreshProfile: () => Promise<void>;
};

const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [currentProject, setCurrentProject] = useState<CurrentProject | null>(
    null,
  );
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    (async () => {
      try {
        const [storedToken, storedUser] = await Promise.all([
          AsyncStorage.getItem(TOKEN_KEY),
          AsyncStorage.getItem(USER_KEY),
        ]);
        if (storedToken) setToken(storedToken);
        if (storedUser) setUser(JSON.parse(storedUser) as User);
      } finally {
        setIsLoading(false);
      }
    })();
  }, []);

  const logout = useCallback(async () => {
    try {
      await api.post('/logout');
    } catch {
      // Ignore network errors on logout — we still clear locally.
    }
    await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
    setToken(null);
    setUser(null);
    setCurrentProject(null);
  }, []);

  useEffect(() => {
    setUnauthorizedHandler(() => {
      setToken(null);
      setUser(null);
      setCurrentProject(null);
    });
  }, []);

  const login = useCallback<AuthContextValue['login']>(
    async (phone, password) => {
      try {
        const res = await api.post<{
          access_token?: string;
          user?: User;
          message?: string;
          errors?: unknown;
        }>('/login', { phone, password });
        const data = res.data;
        if (!data.access_token || !data.user) {
          const errMsg =
            typeof data.message === 'string'
              ? data.message
              : 'Invalid credentials';
          return { ok: false, message: errMsg };
        }
        await AsyncStorage.setItem(TOKEN_KEY, data.access_token);
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(data.user));
        setToken(data.access_token);
        setUser(data.user);
        return { ok: true };
      } catch (err) {
        const e = err as {
          message?: string;
          code?: string;
          response?: { status?: number; data?: { message?: string } };
          config?: { baseURL?: string; url?: string };
        };
        // Verbose log so the metro/console makes the failure mode obvious.
        console.log('[login] failed', {
          url: `${e.config?.baseURL ?? ''}${e.config?.url ?? ''}`,
          code: e.code,
          status: e.response?.status,
          message: e.message,
          serverMessage: e.response?.data?.message,
        });
        if (e.response?.status === 401) {
          return {
            ok: false,
            message: e.response.data?.message ?? 'Invalid Credentials',
          };
        }
        if (e.code === 'ECONNABORTED') {
          return {
            ok: false,
            message: 'Request timed out — check the API URL and network.',
          };
        }
        if (e.code === 'ERR_NETWORK') {
          return {
            ok: false,
            message:
              "Network error — couldn't reach the server. Check your internet connection and try again.",
          };
        }
        return {
          ok: false,
          message:
            e.response?.data?.message ??
            e.message ??
            'Could not reach the server',
        };
      }
    },
    [],
  );

  const refreshProfile = useCallback(async () => {
    try {
      const res = await api.get<{
        user: User;
        current_project?: CurrentProject | null;
      }>('/profile');
      if (res.data?.user) {
        setUser(res.data.user);
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(res.data.user));
      }
      setCurrentProject(res.data?.current_project ?? null);
    } catch {
      // Stale local data is acceptable; 401s already handled by interceptor.
    }
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      currentProject,
      isLoading,
      login,
      logout,
      refreshProfile,
    }),
    [user, token, currentProject, isLoading, login, logout, refreshProfile],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used inside AuthProvider');
  return ctx;
}
