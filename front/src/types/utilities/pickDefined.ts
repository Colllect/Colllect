/**
 * Remove never value from object keys
 */
type PickDefined<T> = Pick<T,
	{ [K in keyof T]: T[K] extends never ? never : K }[keyof T]>;

export default PickDefined
