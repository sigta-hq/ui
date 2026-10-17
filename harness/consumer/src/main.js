import './app.css';
import { hydrate, mount } from 'svelte';
import App from './App.svelte';

const target = document.getElementById('app');
window.__app = target.firstElementChild ? hydrate(App, { target }) : mount(App, { target });
window.__ready = true;
