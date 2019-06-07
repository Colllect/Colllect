export function base64UriDecode(encodedUri: string): string {
  return atob(decodeURIComponent(encodedUri))
}
