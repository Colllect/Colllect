<template>
	<div class="c-colllect-input" :class="classes">
		<label :for="id" class="c-colllect-input--label">
			<slot></slot>
		</label>

		<div class="c-colllect-input--input-border-wrap">
			<div class="c-colllect-input--input-shadow">
				<input :type="type" :id="id" v-model="localValue" ref="input"
							 :disabled="disabled" :autofocus="autofocus" :autocomplete="autocomplete" :placeholder="placeholder"
							 class="c-colllect-input--input" @focus="focus" @blur="blur">

				<span class="c-colllect-input--error" v-if="!disabled && errored && errorMessage">
					{{ errorMessage }}
				</span>
			</div>
		</div>
	</div>
</template>

<script src="./input.ts" type="ts"/>
<style src="./input.scss" type="scss"/>
