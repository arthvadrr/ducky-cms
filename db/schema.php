<?php
return "
CREATE TABLE IF NOT EXISTS users (
id INTEGER PRIMARY KEY AUTOINCREMENT,
username TEXT UNIQUE NOT NULL COLLATE NOCASE CHECK(length(username) BETWEEN 6 AND 32 AND username GLOB '[A-Za-z0-9_-]*'),
password TEXT NOT NULL CHECK(length(password) >= 60 AND length(password) <= 255),
created_at TEXT DEFAULT CURRENT_TIMESTAMP,
session_token TEXT,
token_created_at INTEGER
);

CREATE TABLE IF NOT EXISTS pages (
id INTEGER PRIMARY KEY AUTOINCREMENT,
title TEXT NOT NULL,
slug TEXT UNIQUE NOT NULL,
content TEXT NOT NULL,
type TEXT DEFAULT 'default',
status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('published','draft','trash')),
created_at TEXT DEFAULT CURRENT_TIMESTAMP,
updated_at TEXT DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_pages_status ON pages(status);
CREATE INDEX IF NOT EXISTS idx_pages_updated_at ON pages(updated_at);

CREATE TABLE IF NOT EXISTS media (
id INTEGER PRIMARY KEY AUTOINCREMENT,
filename TEXT NOT NULL,
path TEXT NOT NULL,
extension TEXT,
size INTEGER,
mime_type TEXT,
width INTEGER,
height INTEGER,
alt_text TEXT,
uploaded_at TEXT DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  key TEXT PRIMARY KEY,
  value TEXT
);

CREATE TABLE IF NOT EXISTS menus (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS menu_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  menu_id INTEGER NOT NULL,
  label TEXT NOT NULL,
  page_id INTEGER,
  url TEXT,
  sort_order INTEGER DEFAULT 0,
  FOREIGN KEY (menu_id) REFERENCES menus(id),
  FOREIGN KEY (page_id) REFERENCES pages(id)
);

CREATE TABLE IF NOT EXISTS setup_nonce (
  token TEXT,
  created_at INTEGER,
  used INTEGER DEFAULT 0
);
";