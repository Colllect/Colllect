/**
 * Deep find value type
 * e.g. Get<obj, ["user", "firstname"]>
 */
type Get<T extends any, K extends any[], D = never> = K extends []
	? T
	: K extends [infer A, ...infer B]
		? A extends keyof T
			? Get<T[A], B>
			: D
		: D;

export default Get
