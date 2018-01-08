declare module '*.html' {
  import Vue, { ComponentOptions } from 'vue'
  interface WithRender {
    <V extends Vue>(options: ComponentOptions<V>): ComponentOptions<V>
    <V extends typeof Vue>(component: V): V
  }
  const withRender: WithRender
  export default withRender
}
