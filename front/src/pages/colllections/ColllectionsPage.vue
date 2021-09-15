<template>
	<div class="p-colllect-colllections">
		<h1>Colllections</h1>

		<ul>
			<li v-for="colllection in colllections">
				<router-link :to="{ name: 'colllection', params: { encodedColllectionPath: colllection.encodedColllectionPath } }">
					{{ colllection.name }}
				</router-link>
			</li>
		</ul>
	</div>
</template>

<script src="./colllectionsPage.ts" type="ts"/>
<style src="./colllectionsPage.scss" type="scss"/>

