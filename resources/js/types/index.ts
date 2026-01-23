export type * from './auth';
export type * from './navigation';
export type * from './ui';

export type SharedData = {
    name: string;
    [key: string]: unknown;
};
