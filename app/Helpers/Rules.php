<?php

const RULES_REGISTER = ['username' => 'required', 'password' => 'required', 'role' => 'required'];
const RULES_LOGIN    = ['username' => 'required', 'password' => 'required'];
const RULES_FILE_UPLOAD  = ['file' => 'required','entity_type' => 'required|numeric','entity_id' => 'required|numeric'];
