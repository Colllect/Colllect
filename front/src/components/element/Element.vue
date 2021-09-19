<template>
	<div class="c-colllect-element" :class="classes" :style="style" ref="domElement">
		<div class="c-colllect-element--content">
			<!--
			{{ name }}
			<template v-if="tags.length > 0">
				{{ '#' + tags.join(' #') }}
			</template>
			-->
			<img v-if="isImage"
					 @load="onImageLoaded"
					 :src="(show || isLoaded) ? element.fileUrl : ''"
					 alt="">
		</div>
	</div>
</template>

<script src="./element.ts" type="ts"/>
<style src="./element.scss" type="scss"/>
