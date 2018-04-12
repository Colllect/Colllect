declare module '*.html' {
  import Vue, {ComponentOptions} from 'vue'

  interface WithRender {
    <V extends Vue>(options: ComponentOptions<V>): ComponentOptions<V>

    <V extends typeof Vue>(component: V): V
  }

  const withRender: WithRender
  export default withRender
}

declare module 'tiny-cookie' {
  interface TinyCookieSetterOptions {
    expires?: string | number
    secure?: boolean
    path?: string
    domain?: string
  }

  export function isEnabled(): boolean

  export function get(key: string): string | null

  export function getRaw(key: string): string | null

  export function getAll(): {[key: string]: string}

  export function set(
    key: string,
    value: any,
    encoder?: (content: string) => string,
    options?: TinyCookieSetterOptions,
  ): void

  export function setRaw(key: string, value: any, options?: TinyCookieSetterOptions): void

  export function remove(key: string, options?: { domain?: string }): void
}

declare module 'minigrid' {
  interface MiniGridOptions {
    container?: string | Node
    item?: string | Node
    gutter?: number
  }

  class MiniGrid {
    constructor(params: MiniGridOptions)
    public mount(): void
  }

  export default MiniGrid
}
