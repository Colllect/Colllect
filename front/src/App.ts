import {Component, Vue} from 'vue-property-decorator'
import WithRender from './App.html'

import ColllectHeader from './components/header/Header'

@WithRender
@Component({
  components: {
    ColllectHeader,
  },
})
export default class App extends Vue {
}
