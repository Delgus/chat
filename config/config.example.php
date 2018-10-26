<?php
//DB SETTINGS
const DB_DSN = 'mysql:host=127.0.0.1;dbname=chat';
const DB_USERNAME = 'root';
const DB_PASSWORD = '';

//AUTH
const SECRET_KEY = '20^h.wdkjwheqqqqqqqqqqqqqqqq';
const TOKEN_LIVE = 3600*24*7;
const HOST_NAME = 'http://127.0.0.1:8000';
const AUTH_URL = "http://127.0.0.1:8000/auth";

//CHAT
const WEB_SOCKET = '127.0.0.1:8888';
const CHAT_URL = "http://127.0.0.1:8000/chat";
const MESSAGES_ON_PAGE = 20;
const TIME_FORMAT = "d.m.Y H:i:s";
