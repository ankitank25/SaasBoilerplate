// Form field types

export enum buttonTypes {
  BUTTON = "button",
  SUBMIT = "submit",
  RESET = "reset",
}

export interface selectOption {
  value: string | number;
  label: string | number;
  disabled?: boolean;
}

export type selectOptions = selectOption[];

export type radioStyles = "small-cards" | "cards";

export interface radioOption {
  value: string | number;
  label: string | number;
  description?: string;
  disabled?: boolean;
}

export type radioOptions = radioOption[];

// UI

export enum toastTypes {
  SUCCESS = "green",
  WARNING = "yellow",
  ERROR = "red",
}

export type Toast = {
  message: string;
  type: toastTypes;
  hide?: boolean;
};

export interface listColumnOption {
  value: string | number | boolean;
  label: string | number | boolean;
}

export type listColumnOptions = listColumnOption[];

export interface listColumn {
  label: string;
  type?: string;
  options?: listColumnOptions;
  sortable?: boolean;
  defaultSort?: boolean;
  class?: string;
  noActionValue?: string;
}

export interface listColumns {
  [key: string]: listColumn;
}

export interface listAction {
  action:
    | "edit"
    | "delete"
    | "selectItem"
    | "unSelectItem"
    | "selectAll"
    | "unSelectAll"
    | "bulkDelete";
  label: string;
}

export type listActions = listAction[];

export interface listDeletePrompt {
  title: string;
  message: string;
}

// Api resource types

export interface userNotification {
  id: string;
  entity_id: string;
  type: string;
  message: string;
  status: number;
  created_at: string;
  updated_at: string;
}
export type userNotifications = userNotification[];

export interface userMeta {
  id: number;
  name: string;
  value: string;
  created_at: string;
  updated_at: string;
}

export type userSession = {
  id: number;
  ip: string;
  location: string;
  device: string;
  platform: string;
  browser: string;
  current: boolean;
  created_at: string;
};

export type userSessions = userSession[];

export interface user {
  id: string;
  first_name: string;
  last_name: string;
  initial?: string;
  email: string;
  email_verified_at: string;
  terms_agreed: number;
  twofa_type: string;
  twofa_verified_at?: string;
  notifications: userNotifications;
  role: string;
  status: number;
  meta: { [key: string]: string };
}

export type Users = user[];

export interface Page {
  id: string;
  title: string;
  url_key: string;
  show_title: boolean;
  content: string;
  layout: string;
  meta_title: string;
  meta_description: string;
  status: boolean;
  created_at: string;
  updated_at: string;
}
export type Pages = Page[];

export type MenuItem = {
  id: number;
  title: string;
  url: string;
  label: string;
  icon: string;
  icon_placement: string;
  target: string;
  rel: string;
  status: boolean;
  items?: MenuItem[];
};

export type Menu = {
  id: string;
  title: string;
  items: string;
  status: boolean;
  created_at: string;
  updated_at: string;
};

export type Menus = Menu[];

export type Setting = {
  id: string;
  name: string;
  value: string;
};

export type Settings = {
  config: Setting[];
  resources: {};
};

export interface SpaceUser {
  id: string;
  first_name: string;
  last_name: string;
  email: string;
  role: string;
  role_id: number;
  abilities: string;
}

export interface SpaceRole {
  id: number;
  space_id: number;
  name: string;
  description: string;
  abilities: string;
  created_at: string;
  updated_at: string;
}

export interface SpaceInvitation {
  id: number;
  space_id: number;
  role_id: number;
  user_id: number;
  email: string;
  status: number;
  role: SpaceRole;
  created_at: string;
  updated_at: string;
}

export type Space = {
  id: string;
  owner: string;
  name: string;
  status: boolean;
  created_at: string;
  updated_at: string;
  users: SpaceUser[];
  invitations: SpaceInvitation[];
};

export type Spaces = Space[];

// API Response types

export interface apiResponse<T = void> {
  success: boolean;
  errorCode: string;
  message: string;
  data: T;
}

export type paginationLink = {
  url: string;
  label: string;
  active: boolean;
};
export interface paginationData {
  current_page: number;
  first_page_url: string;
  from: number;
  last_page: number;
  last_page_url: string;
  links: paginationLink[];
  next_page_url: string;
  path: string;
  per_page: number;
  prev_page_url: string;
  to: number;
  total: number;
}
export interface paginatedResponse<T = void> extends paginationData {
  data: T;
}

export interface userResponse {
  user: user;
}

export interface userTokenResponse {
  token: string;
  user: user;
}

export interface verifiedResetTokenResponse {
  token: string;
}

// Form param types

export interface registerParams {
  first_name: string;
  last_name: string;
  email: string;
  password: string;
  password_confirmation: string;
  terms_agreed: number;
}

export interface loginParams {
  email: string;
  password: string;
  remember_me: boolean;
}

export interface resetPasswordParams {
  email: string;
  token: string;
  password: string;
  password_confirmation: string;
}
