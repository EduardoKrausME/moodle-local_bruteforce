<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Brazilian Portuguese language strings.
 *
 * @package    local_bruteforce
 * @copyright  2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['blockduration'] = 'Tempo de bloqueio';
$string['blockduration_desc'] = 'Por quanto tempo um IP ou uma combinação de IP e usuário permanece bloqueada depois de atingir o limite de falhas.';
$string['blocked'] = 'Foram detectadas muitas tentativas de login com falha a partir da sua conexão. Tente novamente em {$a}.';
$string['identitylimit'] = 'Tentativas por IP e usuário';
$string['identitylimit_desc'] = 'Quantidade máxima de falhas para o mesmo usuário a partir do mesmo IP dentro da janela de contagem. Esse limite detecta ataques focados sem bloquear todos os usuários que compartilham o mesmo NAT.';
$string['iplimit'] = 'Tentativas por IP';
$string['iplimit_desc'] = 'Quantidade máxima de falhas originadas do mesmo IP dentro da janela de contagem, independentemente do usuário informado. Em redes com muitos usuários no mesmo IP público, mantenha este limite acima do limite por IP e usuário.';
$string['pluginname'] = 'Brute Force Detect';
$string['privacy:metadata:attempt'] = 'Contadores temporários de segurança utilizados para detectar tentativas repetidas de autenticação com falha.';
$string['privacy:metadata:attempt:attempts'] = 'Quantidade de tentativas de autenticação com falha contadas na janela atual.';
$string['privacy:metadata:attempt:blockeduntil'] = 'Data e hora até a qual a origem permanece bloqueada.';
$string['privacy:metadata:attempt:firstattempt'] = 'Data e hora da primeira tentativa com falha na janela atual.';
$string['privacy:metadata:attempt:ip'] = 'Endereço IP remoto de onde vieram as tentativas de autenticação com falha.';
$string['privacy:metadata:attempt:lastattempt'] = 'Data e hora da tentativa de autenticação com falha mais recente.';
$string['privacy:metadata:attempt:usernamehash'] = 'Hash de mão única do usuário informado, usado para distinguir ataques focados sem armazenar o nome de usuário em texto puro.';
$string['settingsheading'] = 'Proteção de login';
$string['settingsheading_desc'] = 'O plugin observa falhas de autenticação do Moodle e aplica limites independentes para a combinação IP+usuário e para o IP inteiro. Enquanto o contador estiver bloqueado, novas autenticações são recusadas antes da validação da senha.';
$string['taskcleanup'] = 'Limpar contadores expirados de força bruta';
$string['window'] = 'Janela de tentativas';
$string['window_desc'] = 'Período móvel no qual as falhas são acumuladas. Depois de um período sem falhas maior que esta janela, o contador recomeça.';
