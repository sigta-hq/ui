import { render } from 'svelte/server';
import App from './App.svelte';

export const html = () => render(App).body;
