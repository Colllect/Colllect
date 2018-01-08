import {Component, Vue} from 'vue-property-decorator'
import WithRender from './Inbox.html'

@WithRender
@Component
export default class Inbox extends Vue {
}
