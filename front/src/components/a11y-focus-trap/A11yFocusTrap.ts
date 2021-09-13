import Vue from 'vue'
import Component from 'vue-class-component'
import WithRender from './A11yFocusTrap.html'

interface TrapInfo {
  vm: Vue,
  prevTarget: HTMLElement,
}

const FOCUSABLE_SELECTOR = [
  'a[href]',
  'area[href]',
  'button',
  'details',
  'input',
  'iframe',
  'select',
  'textarea',
  '[contentEditable=""]',
  '[contentEditable="true"]',
  '[contentEditable="TRUE"]',
  '[tabindex]',
].map((selector) => selector + ':not([tabindex^="-"]):not([disabled])').join(',')

/**
 * <A11yFocusTrap>
 * - methods: open(), replace(), close(returnFocus)
 * - events: open, gofirst, golast
 * - slots: default slot
 */
@WithRender
@Component
export default class A11yFocusTrap extends Vue {
  private static trapStack: TrapInfo[] = []
  private isMounted: boolean = false

  public open() {
    const prevTarget = document.activeElement as HTMLElement
    A11yFocusTrap.trapStack.push({vm: this, prevTarget})

    const autofocusElement = this.$el.querySelector('[autofocus]') as HTMLElement
    if (autofocusElement) {
      autofocusElement.focus()
    } else {
      this.goFirst()
    }
  }

  public replace() {
    const prevTarget = document.activeElement as HTMLElement
    A11yFocusTrap.trapStack.pop()
    A11yFocusTrap.trapStack.push({vm: this, prevTarget})
    this.goFirst()
  }

  public close(returnFocus: boolean = true) {
    const trap = A11yFocusTrap.trapStack.pop()
    if (!trap) {
      return
    }
    const {prevTarget} = trap
    if (returnFocus) {
      prevTarget.focus()
    }
  }

  public goFirst() {
    const focusableElements = this.getFocusableElements()
    focusableElements[0].focus()
  }

  public goLast() {
    const focusableElements = this.getFocusableElements()
    focusableElements[focusableElements.length - 1].focus()
  }

  private getFocusableElements(): HTMLElement[] {
    const focusableContainer = this.$refs.focusableContainer as HTMLElement
    const focusableElements = Array.from(focusableContainer.querySelectorAll(FOCUSABLE_SELECTOR)) as HTMLElement[]

    return focusableElements
  }

  private trapFocus(event: FocusEvent) {
    const trap = A11yFocusTrap.trapStack[A11yFocusTrap.trapStack.length - 1]
    if (!trap || trap.vm !== this) {
      return
    }

    const root = this.$el
    const {start, end} = this.$refs
    const {target} = event
    if (!root.contains(target as HTMLElement) || target === end) {
      event.preventDefault()
      this.goFirst()
    } else if (target === start) {
      event.preventDefault()
      this.goLast()
    }
  }

  private mounted() {
    this.isMounted = true
    document.addEventListener('focus', this.trapFocus, true)
  }

  private beforeDestroy() {
    if (this.isMounted) {
      document.removeEventListener('focus', this.trapFocus, true)
    }
  }
}
