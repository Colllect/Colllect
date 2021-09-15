<template>
	<div class="c-colllect-element" :class="classes" :style="style" ref="elementNode">
		<div class="c-colllect-element--content">
			<!--
			{{ name }}
			<template v-if="tags.length > 0">
				{{ '#' + tags.join(' #') }}
			</template>
			-->
			<img v-if="isImage"
					 @load="imageLoaded"
					 :src="(show || isLoaded) ? fileUrl : ''"
					 alt="">
		</div>
	</div>
</template>

<script src="./element.ts" type="ts"/>
<style src="./element.scss" type="scss"/>
