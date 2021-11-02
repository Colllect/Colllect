// Extract required keys list
type RequiredKeys<T> = {
	[K in keyof T]-?: T extends Record<K, T[K]> ? K : never;
}[keyof T];

export default RequiredKeys
