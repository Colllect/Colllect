import Vue from 'vue'
import Vuex from 'vuex'
import {getStoreBuilder} from 'vuex-typex'

import {RootState} from '@/src/store/state'

const store = getStoreBuilder<RootState>().vuexStore()
export default store
