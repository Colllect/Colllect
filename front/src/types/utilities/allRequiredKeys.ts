import RequiredKeys from '@/src/types/utilities/requiredKeys'

/**
 * Check if all sub-keys are required in T, else return never
 */
type AllRequiredKey<T> = {
	[K in keyof T]: RequiredKeys<T[K]> extends never ? K : never;
}[keyof T];

export default AllRequiredKey
