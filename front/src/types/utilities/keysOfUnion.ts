// Get all keys of a union type (keyof A | keyof B)
// e.g. KeysOfUnion<A | B>
type KeysOfUnion<T> = T extends T ? keyof T : never;

export default KeysOfUnion
