@extends('layouts.app')
@section('title'){{ __('BIP-39 Converter') }} @endsection
@section('content')
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-2 mb-xl-3">
                <div class="col-auto d-none d-sm-block">
                    <h1 class="h3 mb-3"><strong>{{ __('Generator') }}
                    </h1>
                </div>
            </div>

            @if($errors->any())
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-danger">
                            <div class="alert-message">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-danger">
                            <div class="alert-message">
                                {{ session('error') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if(session('success'))
                <div class="row">
                    <div class="col-xl-6 col-xxl-5">
                        <div class="alert alert-primary">
                            <div class="alert-message">
                                {{ session('success') }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-12">
                    <h2>Mnemonic</h2>
                    <form class="form-horizontal" role="form">
                        <div class="mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <p>You can enter an existing BIP39 mnemonic, or generate a new
                                    random one. Typing your own
                                    twelve words will probably not work how you expect, since the
                                    words require a particular
                                    structure (the last word contains a checksum).</p>
                                <p>
                                    For more info see the
                                    <a href="https://github.com/bitcoin/bips/blob/master/bip-0039.mediawiki"
                                       target="_blank">BIP39 spec</a>.
                                </p>
                            </div>
                        </div>
                        <div class="mb-3 generate-container">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <div class="form-inline">
                                    <div class="input-group-inline">
                                        <span>Generate a random mnemonic</span>:
                                        <button class="btn btn-primary generate"><b>GENERATE</b></button>
                                        <select id="strength" class="strength form-select">
                                            <option value="3">3</option>
                                            <option value="6">6</option>
                                            <option value="9">9</option>
                                            <option value="12">12</option>
                                            <option value="15" selected>15</option>
                                            <option value="18">18</option>
                                            <option value="21">21</option>
                                            <option value="24">24</option>
                                        </select>
                                        <span>words, or enter your own below</span>.
                                        <p class="warning help-block visually-hidden">
                                            <span class="text-danger">
                                                Mnemonics with less than 12 words have low entropy and may be guessed by an attacker.
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="entropy-container visually-hidden">
                            <div class="mb-3 text-danger">
                                <label class="col-sm-2 control-label">Warning</label>
                                <div class="col-sm-10 form-control-static">
                                    <span>Entropy is an advanced feature. Your mnemonic may be insecure if this feature is used incorrectly.</span>
                                    <a href="#entropy-notes">Read more</a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="entropy" class="col-sm-2 control-label">Entropy</label>
                                <div class="col-sm-7">
                                <textarea id="entropy" rows="2" class="entropy private-data form-control"
                                          placeholder="Accepts either binary, base 6, 6-sided dice, base 10, hexadecimal or cards"
                                          autocomplete="off" autocorrect="off" autocapitalize="off"
                                          spellcheck="false"></textarea>
                                    <div class="row filter-warning text-danger visually-hidden">
                                        <p class="col-sm-12">
                                            <strong>
                                                Some characters have been discarded
                                            </strong>
                                        </p>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-3 control-label"><span
                                                class="more-info"
                                                title="Based on estimates from zxcvbn using Filtered Entropy">Time To Crack</span></label>
                                        <div class="crack-time col-sm-3 form-control-static"></div>
                                        <label class="col-sm-3 control-label">Event Count</label>
                                        <div class="event-count col-sm-3 form-control-static"></div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Entropy Type</label>
                                        <div class="type col-sm-3 form-control-static"></div>
                                        <label class="col-sm-3 control-label">Avg Bits Per
                                            Event</label>
                                        <div
                                            class="bits-per-event col-sm-3 form-control-static"></div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-3 control-label">Raw Entropy
                                            Words</label>
                                        <div class="word-count col-sm-3 form-control-static"></div>
                                        <label class="col-sm-3 control-label"><span
                                                class="more-info"
                                                title="Total bits of entropy may be less than indicated if any entropy event uses a weak source.">Total Bits</span></label>
                                        <div class="bits col-sm-3 form-control-static"></div>
                                    </div>
                                    <label class="col-sm-3 control-label">Filtered Entropy</label>
                                    <div
                                        class="filtered private-data col-sm-9 form-control-static"></div>
                                    <label class="col-sm-3 control-label">Raw Binary</label>
                                    <div
                                        class="binary private-data col-sm-9 form-control-static"></div>
                                    <label class="col-sm-3 control-label">Binary Checksum</label>
                                    <div class="checksum private-data col-sm-9 form-control-static">
                                        &nbsp;
                                    </div>
                                    <label class="col-sm-3 control-label">Word Indexes</label>
                                    <div
                                        class="word-indexes private-data col-sm-9 form-control-static">
                                        &nbsp;
                                    </div>
                                    <label class="col-sm-3 control-label">Mnemonic Length</label>
                                    <div class="col-sm-9">
                                        <select class="mnemonic-length form-control">
                                            <option value="raw" selected>Use Raw Entropy (3 words
                                                per 32 bits)
                                            </option>
                                            <option value="12">12 <span>Words</span></option>
                                            <option value="15">15 <span>Words</span></option>
                                            <option value="18">18 <span>Words</span></option>
                                            <option value="21">21 <span>Words</span></option>
                                            <option value="24">24 <span>Words</span></option>
                                        </select>
                                        <p class="weak-entropy-override-warning visually-hidden">
                                            <span class="text-danger">
                                                The mnemonic will appear more secure than it really is.
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <p>Valid entropy values include:</p>
                                    <ul>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="binary">
                                                <strong>Binary</strong> [0-1]<br>101010011
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="base 6">
                                                <strong>Base 6</strong> [0-5]<br>123434014
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="dice">
                                                <strong>Dice</strong> [1-6]<br>62535634
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="base 10">
                                                <strong>Base 10</strong> [0-9]<br>90834528
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="hexadecimal" checked>
                                                <strong>Hex</strong> [0-9A-F]<br>4187a8bfd9
                                            </label>
                                        </li>
                                        <li>
                                            <label>
                                                <input type="radio" name="entropy-type"
                                                       value="card">
                                                <strong>Card</strong> [A2-9TJQK][CDHS]<br>ahqs9dtc
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 checkbox">
                                <label>
                                    <input type="checkbox" class="use-entropy">
                                    <span>Show entropy details</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10 checkbox">
                                <label>
                                    <input type="checkbox" class="privacy-screen-toggle">
                                    <span>Hide all private info</span>
                                </label>
                            </div>
                        </div>
                        <div class="col-lg-10 mb-3">
                           <h5>Mnemonic language</h5>
                            <a href="#english">English</a>
                            <a href="#japanese" title="Japanese">日本語</a>
                            <a href="#spanish" title="Spanish">Español</a>
                            <a href="#chinese_simplified" title="Chinese (Simplified)">中文(简体)</a>
                            <a href="#chinese_traditional" title="Chinese (Traditional)">中文(繁體)</a>
                            <a href="#french" title="French">Français</a>
                            <a href="#italian" title="Italian">Italiano</a>
                            <a href="#korean" title="Korean">한국어</a>
                            <a href="#czech" title="Czech">Čeština</a>
                            <a href="#portuguese" title="Portuguese">Português</a>
                        </div>
                        <div class="mb-3">
                            <label for="phrase" class="col-sm-2 control-label">BIP39
                                Mnemonic</label>
                            <div class="col-sm-10">
                            <textarea id="phrase" class="phrase private-data form-control" data-show-qr
                                      autocomplete="off" autocorrect="off" autocapitalize="off"
                                      spellcheck="false"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="splitMnemonic visually-hidden">
                                <label for="phrase" class="col-sm-2 control-label">BIP39 Split
                                    Mnemonic</label>
                                <div class="col-sm-10">
                                <textarea id="phraseSplit" class="phraseSplit private-data form-control"
                                          title="Only 2 of 3 cards needed to recover." rows="3"></textarea>
                                    <p class="help-block">
                                        <span id="phraseSplitWarn" class="phraseSplitWarn"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-10">
                                <label class="control-label text-weight-normal">
                                    <input type="checkbox" class="showSplitMnemonic">
                                    Show split mnemonic cards
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="passphrase" class="col-sm-2 control-label">BIP39 Passphrase
                                (optional)</label>
                            <div class="col-sm-10">
                            <textarea id="passphrase" class="passphrase private-data form-control" autocomplete="off"
                                      autocorrect="off" autocapitalize="off" spellcheck="false"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="seed" class="col-sm-2 control-label">BIP39 Seed</label>
                            <div class="col-sm-10">
                            <textarea id="seed" class="seed private-data form-control" data-show-qr autocomplete="off"
                                      autocorrect="off" autocapitalize="off" spellcheck="false"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="network-phrase" class="col-sm-2 control-label">Coin</label>
                            <div class="col-sm-10">
                                <select id="network-phrase" class="network form-control">
                                    <!-- populated by javascript -->
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="root-key" class="col-sm-2 control-label">BIP32 Root
                                Key</label>
                            <div class="col-sm-10">
                            <textarea id="root-key" class="root-key private-data form-control" data-show-qr
                                      autocomplete="off" autocorrect="off" autocapitalize="off"
                                      spellcheck="false"></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-10">
                                <label class="control-label text-weight-normal">
                                    <input type="checkbox" class="showBip85"/>
                                    Show BIP85
                                </label>
                            </div>
                        </div>

                        <div class="mb-3 bip85 visually-hidden">
                            <div class="mb-3 text-danger">
                                <label class="col-sm-2 control-label">Warning</label>
                                <div class="col-sm-10 form-control-static">
                                    This is an advanced feature and should only be used if you
                                    understand what it does.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="col-sm-2"></label>
                                <div class="col-sm-10">
                                    <p>
                                        The value of the "BIP85 Child Key" field shown below is not
                                        used
                                        elsewhere on this page. It can be used as a new key.
                                    </p>
                                    <p>
                                        In case of the BIP39 application, you can paste it into the
                                        "BIP39 Mnemonic"
                                        field to use it as a new mnemonic.
                                    </p>
                                    <p>
                                        Please read the
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0085.mediawiki"
                                           target="_blank">
                                            BIP85 spec
                                        </a>
                                        for more information.
                                    </p>
                                </div>
                            </div>
                            <label for="bip85-application" class="col-sm-2 control-label">BIP85
                                Application</label>
                            <div class="col-sm-10">
                                <select id="bip85-application" class="form-control">
                                    <option value="bip39" selected>BIP39</option>
                                    <option value="wif">WIF</option>
                                    <option value="xprv">Xprv</option>
                                    <option value="hex">Hex</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 bip85 bip85-mnemonic-language-input visually-hidden">
                            <label for="bip85-mnemonic-language" class="col-sm-2 control-label">BIP85
                                Mnemonic
                                Language</label>
                            <div class="col-sm-10 languages">
                                <select id="bip85-mnemonic-language" class="strength form-control">
                                    <option value="0" selected>English</option>
                                    <!--<option value="1">日本語</option>
                                    <option value="2">한국어</option>
                                    <option value="3">Español</option>
                                    <option value="4">中文(简体)</option>
                                    <option value="5">中文(繁體)</option>
                                    <option value="6">Français</option>
                                    <option value="7">Italiano</option>
                                    <option value="8">Čeština</option>
                                    <option value="9">Português</option>-->
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 bip85 bip85-mnemonic-length-input visually-hidden">
                            <label for="bip85-mnemonic-length" class="col-sm-2 control-label">BIP85
                                Mnemonic Length</label>
                            <div class="col-sm-10">
                                <select id="bip85-mnemonic-length" class="strength form-control">
                                    <option value="12" selected>12</option>
                                    <option value="18">18</option>
                                    <option value="24">24</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 bip85 visually-hidden">
                            <span class="bip85-bytes-input">
                                <label for="bip85-bytes" class="col-sm-2 control-label">BIP85 Bytes</label>
                                <div class="col-sm-10">
                                    <input id="bip85-bytes" type="text" class="change form-control" value="64"/>
                                </div>
                            </span>
                        </div>

                        <div class="mb-3 bip85 bip85-index-input visually-hidden">
                            <label for="bip85-index" class="col-sm-2 control-label">BIP85
                                Index</label>
                            <div class="col-sm-10">
                                <input id="bip85-index" type="text" class="change form-control"
                                       value="0"/>
                            </div>
                        </div>

                        <div class="mb-3 bip85 visually-hidden">
                            <label for="phrase" class="col-sm-2 control-label">BIP85 Child
                                Key</label>
                            <div class="col-sm-10">
                                <textarea
                                    id="bip85Field"
                                    data-show-qr
                                    class="bip85Field private-data form-control"
                                    title="BIP85 Child Key"
                                    rows="3"
                                ></textarea>
                            </div>
                        </div>

                        <div class="mb-3 litecoin-ltub-container visually-hidden">
                            <label for="litecoin-use-ltub"
                                   class="col-sm-2 control-label">Prefixes</label>
                            <div class="col-sm-10 checkbox">
                                <label>
                                    <input type="checkbox" id="litecoin-use-ltub"
                                           class="litecoin-use-ltub"
                                           checked="checked">
                                    Use <code>Ltpv / Ltub</code> instead of <code>xprv / xpub</code>
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-12">
                    <h2>Derivation Path</h2>
                    <ul class="derivation-type nav nav-tabs" role="tablist">
                        <li class="nav-item" id="bip32-tab">
                            <button class="nav-link" role="tab"  data-bs-toggle="tab" data-bs-target="#bip32" data-toggle="tab">BIP32</button>
                        </li>
                        <li id="bip44-tab" class="nav-item">
                            <button class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#bip44" data-toggle="tab">BIP44</button>
                        </li>
                        <li id="bip49-tab" class="nav-item">
                            <button class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#bip49" data-toggle="tab">BIP49</button>
                        </li>
                        <li id="bip84-tab" class="nav-item">
                            <button class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#bip84" data-toggle="tab">BIP84</button>
                        </li>
                        <li id="bip141-tab" class="nav-item">
                            <button class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#bip141" data-toggle="tab">BIP141</button>
                        </li>
                    </ul>
                    <div class="derivation-type tab-content">
                        <div id="bip44" class="tab-pane active">
                            <form class="form-horizontal" role="form">
                                <br>
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <p>
                                        For more info see the
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki"
                                           target="_blank">BIP44 spec</a>.
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="purpose-bip44" class="col-sm-2 control-label">
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#purpose"
                                           target="_blank">Purpose</a>
                                    </label>
                                    <div class="col-sm-10">
                                        <input id="purpose-bip44" type="text"
                                               class="purpose form-control" value="44"
                                               readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="coin-bip44" class="col-sm-2 control-label">
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#registered-coin-types"
                                           target="_blank">Coin</a>
                                    </label>
                                    <div class="col-sm-10">
                                        <input id="coin-bip44" type="text" class="coin form-control"
                                               value="0" readonly>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="account-bip44" class="col-sm-2 control-label">
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#account"
                                           target="_blank">Account</a>
                                    </label>
                                    <div class="col-sm-10">
                                        <input id="account-bip44" type="text"
                                               class="account form-control" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="change-bip44" class="col-sm-2 control-label">
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#change"
                                           target="_blank">External / Internal</a>
                                    </label>
                                    <div class="col-sm-10">
                                        <input id="change-bip44" type="text"
                                               class="change form-control" value="0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="col-sm-2 control-label">
                                    </label>
                                    <div class="col-sm-10">
                                        <p>The account extended keys can be used for importing to
                                            most BIP44 compatible
                                            wallets, such as mycelium or electrum.</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="account-xprv" class="col-sm-2 control-label">
                                        <span>Account Extended Private Key</span>
                                    </label>
                                    <div class="col-sm-10">
                                    <textarea id="account-xprv-bip44" type="text"
                                              class="account-xprv private-data form-control" readonly data-show-qr
                                              autocomplete="off" autocorrect="off" autocapitalize="off"
                                              spellcheck="false"></textarea>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="account-xpub" class="col-sm-2 control-label">
                                        <span>Account Extended Public Key</span>
                                    </label>
                                    <div class="col-sm-10">
                                    <textarea id="account-xpub-bip44" type="text" class="account-xpub form-control"
                                              readonly data-show-qr autocomplete="off" autocorrect="off"
                                              autocapitalize="off" spellcheck="false"></textarea>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="col-sm-2 control-label">
                                    </label>
                                    <div class="col-sm-10">
                                        <p>The BIP32 derivation path and extended keys are the basis
                                            for the derived
                                            addresses.</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="bip44-path" class="col-sm-2 control-label">BIP32
                                        Derivation Path</label>
                                    <div class="col-sm-10">
                                        <input id="bip44-path" type="text" class="path form-control"
                                               value="m/44'/0'/0'/0"
                                               readonly="readonly">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="bip32" class="tab-pane">
                            <form class="form-horizontal" role="form">
                                <br>
                                <div class="col-sm-2"></div>
                                <div class="col-sm-10">
                                    <p>
                                        For more info see the
                                        <a href="https://github.com/bitcoin/bips/blob/master/bip-0032.mediawiki"
                                           target="_blank">BIP32 spec</a>
                                    </p>
                                </div>
                                <div class="mb-3">
                                    <label for="bip32-client"
                                           class="col-sm-2 control-label">Client</label>
                                    <div class="col-sm-10">
                                        <select id="bip32-client" class="client form-control">
                                            <option value="custom">Custom derivation path</option>
                                            <!-- populated by javascript -->
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="bip32-path" class="col-sm-2 control-label">BIP32
                                        Derivation Path</label>
                                    <div class="col-sm-10">
                                        <input id="bip32-path" type="text" class="path form-control"
                                               value="m/0">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="core-path" class="col-sm-2 control-label">Bitcoin
                                        Core</label>
                                    <div class="col-sm-10">
                                        <p class="form-control no-border">
                                            Use path <code>m/0'/0'</code> with hardened addresses.
                                        </p>
                                        <p class="form-control no-border">
                                            For more info see the
                                            <a href="https://github.com/bitcoin/bitcoin/pull/8035"
                                               target="_blank">Bitcoin
                                                Core BIP32 implementation</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="core-path"
                                           class="col-sm-2 control-label">Multibit</label>
                                    <div class="col-sm-10">
                                        <p class="form-control no-border">
                                            <span>Use path <code>m/0'/0</code>.</span>
                                            <span>For change addresses use path <code>m/0'/1</code>.</span>
                                        </p>
                                        <p class="form-control no-border">
                                            <span>For more info see</span>
                                            <a href="https://multibit.org/" target="_blank">MultiBit
                                                HD</a>
                                        </p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="col-sm-2 control-label">Block Explorers</label>
                                    <div class="col-sm-10">
                                        <p class="form-control no-border">
                                            <span>Use path <code>m/44'/0'/0'</code>.</span>
                                            <span>Only enter the <code>xpub</code> extended key into block explorer search fields, never the <code>xprv</code> key.</span>
                                        </p>
                                        <p class="form-control no-border">
                                            <span>Can be used with</span>:
                                            <a href="https://blockchain.info/" target="_blank">blockchain.info</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="bip49" class="tab-pane">
                            <form class="form-horizontal" role="form">
                                <br>
                                <div class="unavailable visually-hidden">
                                    <div class="mb-3">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <p>BIP49 is unavailable for this coin.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="available">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <p>
                                            For more info see the
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0049.mediawiki"
                                               target="_blank">BIP49 spec</a>.
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="purpose-bip49" class="col-sm-2 control-label">
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#purpose"
                                               target="_blank">Purpose</a>
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="purpose-bip49" type="text"
                                                   class="purpose form-control" value="49"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="coin-bip49" class="col-sm-2 control-label">
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#registered-coin-types"
                                               target="_blank">Coin</a>
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="coin-bip49" type="text"
                                                   class="coin form-control" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-bip49" class="col-sm-2 control-label">
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#account"
                                               target="_blank">Account</a>
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="account-bip49" type="text"
                                                   class="account form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="change-bip49" class="col-sm-2 control-label">
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0044.mediawiki#change"
                                               target="_blank">External / Internal</a>
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="change-bip49" type="text"
                                                   class="change form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-sm-2 control-label">
                                        </label>
                                        <div class="col-sm-10">
                                            <p>The account extended keys can be used for importing
                                                to most BIP49 compatible
                                                wallets.</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-xprv" class="col-sm-2 control-label">
                                            <span>Account Extended Private Key</span>
                                        </label>
                                        <div class="col-sm-10">
                                        <textarea id="account-xprv-bip49" type="text"
                                                  class="account-xprv private-data form-control" readonly data-show-qr
                                                  autocomplete="off" autocorrect="off" autocapitalize="off"
                                                  spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-xpub" class="col-sm-2 control-label">
                                            <span>Account Extended Public Key</span>
                                        </label>
                                        <div class="col-sm-10">
                                        <textarea id="account-xpub-bip49" type="text" class="account-xpub form-control"
                                                  readonly data-show-qr autocomplete="off" autocorrect="off"
                                                  autocapitalize="off" spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-sm-2 control-label">
                                        </label>
                                        <div class="col-sm-10">
                                            <p>The BIP32 derivation path and extended keys are the
                                                basis for the derived
                                                addresses.</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bip49-path" class="col-sm-2 control-label">BIP32
                                            Derivation Path</label>
                                        <div class="col-sm-10">
                                            <input id="bip49-path" type="text"
                                                   class="path form-control"
                                                   value="m/49'/0'/0'/0" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="bip141" class="tab-pane">
                            <form class="form-horizontal" role="form">
                                <br>
                                <div class="unavailable visually-hidden">
                                    <div class="mb-3">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <p>BIP141 is unavailable for this coin.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="available">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <p>
                                            For more info see the
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0141.mediawiki"
                                               target="_blank">BIP141 spec</a>
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bip141-path" class="col-sm-2 control-label">BIP32
                                            Derivation
                                            Path</label>
                                        <div class="col-sm-10">
                                            <input id="bip141-path" type="text"
                                                   class="bip141-path form-control"
                                                   value="m/0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-sm-2 control-label">Script
                                            Semantics</label>
                                        <div class="col-sm-10">
                                            <select class="form-control bip141-semantics">
                                                <option value="p2wpkh">P2WPKH</option>
                                                <option value="p2wpkh-p2sh" selected>P2WPKH nested
                                                    in P2SH
                                                </option>
                                                <option value="p2wsh">P2WSH (1-of-1 multisig)
                                                </option>
                                                <option value="p2wsh-p2sh">P2WSH nested in P2SH
                                                    (1-of-1 multisig)
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="bip84" class="tab-pane">
                            <form class="form-horizontal" role="form">
                                <br>
                                <div class="unavailable visually-hidden">
                                    <div class="mb-3">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-10">
                                            <p>BIP84 is unavailable for this coin.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="available">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <p>
                                            For more info see the
                                            <a href="https://github.com/bitcoin/bips/blob/master/bip-0084.mediawiki"
                                               target="_blank">BIP84 spec</a>.
                                        </p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="purpose-bip84" class="col-sm-2 control-label">
                                            Purpose
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="purpose-bip84" type="text"
                                                   class="purpose form-control" value="84"
                                                   readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="coin-bip84" class="col-sm-2 control-label">
                                            Coin
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="coin-bip84" type="text"
                                                   class="coin form-control" value="0" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-bip84" class="col-sm-2 control-label">
                                            Account
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="account-bip84" type="text"
                                                   class="account form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="change-bip84" class="col-sm-2 control-label">
                                            External / Internal
                                        </label>
                                        <div class="col-sm-10">
                                            <input id="change-bip84" type="text"
                                                   class="change form-control" value="0">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-sm-2 control-label">
                                        </label>
                                        <div class="col-sm-10">
                                            <p>The account extended keys can be used for importing
                                                to most BIP84 compatible
                                                wallets.</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-xprv" class="col-sm-2 control-label">
                                            <span>Account Extended Private Key</span>
                                        </label>
                                        <div class="col-sm-10">
                                        <textarea id="account-xprv-bip84" type="text"
                                                  class="account-xprv private-data form-control" readonly data-show-qr
                                                  autocomplete="off" autocorrect="off" autocapitalize="off"
                                                  spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account-xpub" class="col-sm-2 control-label">
                                            <span>Account Extended Public Key</span>
                                        </label>
                                        <div class="col-sm-10">
                                        <textarea id="account-xpub-bip84" type="text" class="account-xpub form-control"
                                                  readonly data-show-qr autocomplete="off" autocorrect="off"
                                                  autocapitalize="off" spellcheck="false"></textarea>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="col-sm-2 control-label">
                                        </label>
                                        <div class="col-sm-10">
                                            <p>The BIP32 derivation path and extended keys are the
                                                basis for the derived
                                                addresses.</p>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="bip84-path" class="col-sm-2 control-label">BIP32
                                            Derivation Path</label>
                                        <div class="col-sm-10">
                                            <input id="bip84-path" type="text"
                                                   class="path form-control"
                                                   value="m/84'/0'/0'/0" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <form class="form-horizontal" role="form">
                        <div class="mb-3">
                            <label for="extended-priv-key" class="col-sm-2 control-label">BIP32
                                Extended Private Key</label>
                            <div class="col-sm-10">
                            <textarea id="extended-priv-key" class="extended-priv-key private-data form-control"
                                      readonly="readonly" data-show-qr autocomplete="off" autocorrect="off"
                                      autocapitalize="off" spellcheck="false"></textarea>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="extended-pub-key" class="col-sm-2 control-label">BIP32
                                Extended Public Key</label>
                            <div class="col-sm-10">
                            <textarea id="extended-pub-key" class="extended-pub-key form-control" readonly="readonly"
                                      data-show-qr autocomplete="off" autocorrect="off" autocapitalize="off"
                                      spellcheck="false"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-12">
                    <h2>Derived Addresses</h2>
                    <p>Note these addresses are derived from the BIP32 Extended Key</p>
                </div>
                <div class="col-md-12 bch-addr-type-container visually-hidden">
                    <div class="radio">
                        <label>
                            <input type="radio" value="cashaddr" name="bch-addr-type"
                                   class="use-bch-cashaddr-addresses"
                                   checked="checked">
                            <span>Use CashAddr addresses for Bitcoin Cash (ie starting with 'q' instead of '1')</span>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" value="bitpay" name="bch-addr-type"
                                   class="use-bch-bitpay-addresses">
                            <span>Use BitPay-style addresses for Bitcoin Cash (ie starting with 'C' instead of '1')</span>
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" value="legacy" name="bch-addr-type"
                                   class="use-bch-legacy-addresses">
                            <span>Use legacy addresses for Bitcoin Cash (ie starting with '1')</span>
                        </label>
                    </div>
                </div>
                <div class="col-md-10 mb-3">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="use-bip38">
                            <span>Encrypt private keys using BIP38 and this password:</span>
                        </label>
                        <input class="bip38-password private-data form-control" autocomplete="off"
                               autocorrect="off" autocapitalize="off"
                               spellcheck="false">
                        <small>Enabling BIP38 means each key will take several minutes to generate.</small>
                    </div>
                </div>
                <div class="col-md-10 mb-3">
                    <div class="checkbox">
                        <label>
                            <input class="hardened-addresses" type="checkbox">
                            <span>Use hardened addresses</span>
                        </label>
                    </div>
                </div>
                <ul class="addresses-type nav nav-tabs" role="tablist">
                    <li id="table-tab" class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#table" type="button"  role="tab" data-toggle="tab">Table</button>
                    </li>
                    <li id="csv-tab" class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#csv" type="button" role="tab" data-toggle="tab">CSV</button>
                    </li>
                </ul>
                <div class="addresses-type tab-content">
                    <div id="table" class="tab-pane active">
                        <div class="col-md-12">
                            <div class="table-responsive small">
                                <table class="table table-striped table-sm">
                                    <thead>
                                    <tr>
                                        <th>
                                            <div class="input-group">
                                                <span>Path</span>&nbsp;&nbsp;
                                                <button class="index-toggle btn btn-sm btn-secondary">Toggle
                                                </button>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="input-group">
                                                <span>Address</span>&nbsp;&nbsp;
                                                <button class="address-toggle btn btn-sm btn-secondary">Toggle
                                                </button>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="input-group">
                                                <span>Public Key</span>&nbsp;&nbsp;
                                                <button class="public-key-toggle btn btn-sm btn-secondary">Toggle
                                                </button>
                                            </div>
                                        </th>
                                        <th>
                                            <div class="input-group">
                                                <span>Private Key</span>&nbsp;&nbsp;
                                                <button class="private-key-toggle btn btn-sm btn-secondary">Toggle
                                                </button>
                                            </div>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody class="addresses monospace">
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div id="csv" class="tab-pane">
                        <div class="col-md-12">
                        <textarea class="csv form-control" rows="25" readonly autocomplete="off" autocorrect="off"
                                  autocapitalize="off" spellcheck="false"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <span>Show</span>
            <div class="col-lg-2">
            <input type="number" class="form-control rows-to-add" value="20">
            <button class="more btn btn-sm btn-secondary">more rows</button>
            <span>starting from index</span>
            <input type="number" class="form-control more-rows-start-index">
            <span>(leave blank to generate from next index)</span>
            </div>

        </div>

        <div class="qr-container visually-hidden">
            <div class="qr-hint bg-primary visually-hidden">Click field to hide QR</div>
            <div class="qr-hint bg-primary">Click field to show QR</div>
            <div class="qr-hider visually-hidden">
                <div class="qr-image"></div>
                <div class="qr-warning bg-primary">Caution: Scanner may keep history</div>
            </div>
        </div>

        <div class="feedback-container">
            <div class="feedback">Loading...</div>
        </div>
    </main>

@endsection
@section('styles')
    <style>
        .feedback-container {
            position: fixed;
            top: 0;
            width: 100%;
            text-align: center;
            z-index: 4;
        }

        .feedback {
            display: table;
            padding: 0.5em 1em;
            background-color: orange;
            margin: 0 auto;
            font-size: 2em;
            color: #444;
            border: 2px solid #555;
            border-top: 0;
            border-bottom-left-radius: 20px 20px;
            border-bottom-right-radius: 20px 20px;
        }

        .no-border {
            border: 0;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .0);
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .0);
        }

        .phrase {
            word-break: keep-all;
        }

        .generate-container .strength {
            /* override mobile width from bootstrap */
            width: auto !important;
            display: inline-block;
        }

        .languages a {
            padding-left: 10px;
        }

        .languages a:nth-of-type(1) {
            margin-left: -12px;
            padding-left: 0;
        }

        .monospace {
        }

        .entropy-container .filtered,
        .entropy-container .binary {
            word-wrap: break-word;
        }

        .entropy-container li {
            padding-bottom: 5px;
        }

        .card-suit {
            font-size: 19px;
            line-height: 0;
        }

        .card-suit.club {
            color: #009F00;
        }

        .card-suit.diamond {
            color: #3D5DC4;
        }

        .card-suit.heart {
            color: #F00;
        }

        .card-suit.spade {
            color: #000;
        }

        .qr-container {
            position: fixed;
            top: 0;
            right: 0;
            text-align: center;
            background-color: #FFF;
            border: 1px solid #CCC;
        }

        .qr-image {
            margin: 5px;
        }

        .qr-hint,
        .qr-warning {
            padding: 2px;
            max-width: 320px;
        }

        .more-info {
            cursor: help;
            border-bottom: 1px dashed #000;
            text-decoration: none;
        }

        .version {
            position: absolute;
            top: 5px;
            right: 5px;
        }

        .csv {
            margin-top: 20px;
            margin-bottom: 20px;
            white-space: pre;
            overflow-wrap: normal;
            overflow-x: scroll;
            font-family: monospace;
        }

        .visual-privacy .private-data {
            display: none;
        }

        .text-weight-normal {
            font-weight: normal !important;
        }
    </style>
@endsection
@push('scripts')
    <script type="text/template" id="address-row-template">
        <tr>
            <td class="index"><span></span></td>
            <td class="address"><span data-show-qr></span></td>
            <td class="pubkey"><span data-show-qr></span></td>
            <td class="privkey private-data"><span data-show-qr></span></td>
        </tr>
    </script>
    <script src="{{ asset('static/js/bip39/bip39-libs.js') }}"></script>
    <script src="{{ asset('static/js/bip39/bitcoinjs-extensions.js') }}"></script>
    <script src="{{ asset('static/js/bip39/segwit-parameters.js') }}"></script>
    <script src="{{ asset('static/js/bip39/ripple-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/jingtum-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/casinocoin-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/cosmos-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/eos-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/fio-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/xwc-util.js') }}"></script>
    <script src="{{ asset('static/js/bip39/sjcl-bip39.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_english.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_japanese.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_spanish.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_chinese_simplified.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_chinese_traditional.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_french.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_italian.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_korean.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_czech.js') }}"></script>
    <script src="{{ asset('static/js/bip39/wordlist_portuguese.js') }}"></script>
    <script src="{{ asset('static/js/bip39/jsbip39.js') }}"></script>
    <script src="{{ asset('static/js/bip39/entropy.js') }}"></script>
    <script src="{{ asset('static/js/bip39/index.js') }}"></script>
@endpush
