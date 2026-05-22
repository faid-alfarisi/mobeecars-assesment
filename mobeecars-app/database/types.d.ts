export interface Car {
  id: number;
  brand: string;
  model: string;
  type: string;
  image: string;
  local_image?: string | null;
}

export interface UserPreference {
  id?: number;
  car_id: number;
  liked: number;
  synced: number;
  synced_at: number;
  created_at?: number;
}

export interface MostLikedCar {
  favorite_brand: string | null;
  favorite_model: string | null;
  favorite_type: string | null;
}
