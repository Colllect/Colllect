<template>
	<header class="c-colllect-header">
		<div class="c-colllect-header--fixed">
			<router-link :to="{ name: 'inbox' }" class="c-colllect-header--logo">
				<svg viewBox="0 0 267 119" xmlns="http://www.w3.org/2000/svg" class="c-colllect-header--logo-image">
					<use xlink:href="#colllect-logo"/>
				</svg>
			</router-link>

			<nav class="c-colllect-header--menu">
				<ul>
					<li>
						<router-link :to="{ name: 'inbox' }" class="c-colllect-header--menu-item">Inbox</router-link>
					</li>
					<li>
						<router-link :to="{ name: 'colllections' }" class="c-colllect-header--menu-item">Colllections</router-link>
					</li>
				</ul>
			</nav>

			<div class="c-colllect-header--right">
				<ul class="c-colllect-header--right-actions">
					<li>
						<router-link :to="{ name: 'inbox' }" class="c-colllect-header--right-actions-item">Add an Element</router-link>
					</li>
					<li>
						<a href="/oauth2/dropbox" class="c-colllect-header--right-actions-item">Link your Dropbox</a>
					</li>
				</ul>
				<div class="c-colllect-header--right-user">
					<template v-if="isAuthenticated">
						{{ nickname }}
						<form action="/logout" method="post">
							<button type="submit">Logout</button>
						</form>
					</template>
					<template v-else>
						<a href="/login">Login</a>
					</template>
				</div>
			</div>
		</div>
	</header>
</template>

<script src="./header.ts" type="ts"/>
<style src="./header.scss" type="scss"/>
