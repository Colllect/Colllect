declare module '*.html' {
  import Vue, {ComponentOptions} from 'vue'

  interface WithRender {
    <V extends Vue>(options: ComponentOptions<V>): ComponentOptions<V>

    <V extends typeof Vue>(component: V): V
  }

  const withRender: WithRender
  export default withRender
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
