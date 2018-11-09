@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <h3 id="status">Status: Loading...</h3>
                    <h5 id="address">0x00</h5>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @foreach([
                        'eventName' => 'Event name',
                        'timestamp' => 'Time stamp',
                        'place' => 'Place',
                        'distance' => 'Distance',
                        'time' => 'Time',
                        'description' => 'Description'
                    ] as $field=>$title)
                        <div class="form-group row">
                            <label for="{{$field}}" class="col-md-4 col-form-label text-md-right">{{ __($title) }}</label>
                            <div class="col-md-6">
                                <input id="{{$field}}" type="text" class="form-control{{ $errors->has($field) ? ' is-invalid' : '' }}" name="{{$field}}" required>
                                @if ($errors->has($field))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first($field) }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="row" style="margin-top: 10px; margin: auto;">
                        <button class='btn btn-success mb-3' id="create-stat-button" onclick="createSportsStat()" disabled>Create stat</button>
                    </div>
                    <div class="row" style="margin-top: 10px; margin: auto">
                        <h4 id='create-stat-error' style='color: red;'></h4>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row" style="margin-top: 10px; margin: auto;">
                        <button class='btn btn-success mb-3' id="lookup-stat-button" onclick="statsLookupButtonClicked()" disabled>Lookup your stat(s)</button>
                    </div>
                    <div class="row" style="margin-top: 10px; margin: auto">
                        <h4 id='lookup-stats-error' style='color: red;'></h4>
                        <h4 id='lookup-stats-result' style='color: green;'></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let SportsStat, sportsStat;
    let error = false;
    (async () => {
        // Modern dapp browsers...
        if (ethereum) {
            web3 = new Web3(ethereum);
            try {
                await ethereum.enable();
                document.getElementById('status').innerText = 'Connected! Via Web3 injection.';
            } catch (error) {
                document.getElementById('status').innerText = 'Error connecting to Web3 injection';
                error = true;
            }
        } else {
            document.getElementById('status').innerText = 'Error! No Ethereum in browser';
            error = true;
        }

        if (!error) {
            web3.eth.getAccounts(async (error, accounts) => {
                if (error) {
                    return;
                }
                document.getElementById('address').innerText = 'Address: ' + accounts[0];
            });
            document.querySelector('#create-stat-button').disabled = false;
            document.querySelector('#lookup-stat-button').disabled = false;
        }
        // The interface definition for your smart contract (the ABI)
        // ABI comes from compiled contracts on smart_contracts/build/contracts/SportStat.json
        SportsStat = web3.eth.contract(
            [
                {
                    "constant": true,
                    "inputs": [],
                    "name": "statDelim",
                    "outputs": [
                        {
                            "name": "",
                            "type": "string"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "runDelim",
                    "outputs": [
                        {
                            "name": "",
                            "type": "string"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [
                        {
                            "name": "",
                            "type": "address"
                        },
                        {
                            "name": "",
                            "type": "uint256"
                        }
                    ],
                    "name": "addressToStats",
                    "outputs": [
                        {
                            "name": "eventName",
                            "type": "string"
                        },
                        {
                            "name": "timestamp",
                            "type": "string"
                        },
                        {
                            "name": "place",
                            "type": "string"
                        },
                        {
                            "name": "distance",
                            "type": "string"
                        },
                        {
                            "name": "time",
                            "type": "string"
                        },
                        {
                            "name": "description",
                            "type": "string"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                },
                {
                    "constant": false,
                    "inputs": [],
                    "name": "renounceOwnership",
                    "outputs": [],
                    "payable": false,
                    "stateMutability": "nonpayable",
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "owner",
                    "outputs": [
                        {
                            "name": "",
                            "type": "address"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "isOwner",
                    "outputs": [
                        {
                            "name": "",
                            "type": "bool"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                },
                {
                    "constant": false,
                    "inputs": [
                        {
                            "name": "newOwner",
                            "type": "address"
                        }
                    ],
                    "name": "transferOwnership",
                    "outputs": [],
                    "payable": false,
                    "stateMutability": "nonpayable",
                    "type": "function"
                },
                {
                    "anonymous": false,
                    "inputs": [
                        {
                            "indexed": true,
                            "name": "previousOwner",
                            "type": "address"
                        },
                        {
                            "indexed": true,
                            "name": "newOwner",
                            "type": "address"
                        }
                    ],
                    "name": "OwnershipTransferred",
                    "type": "event"
                },
                {
                    "constant": false,
                    "inputs": [
                        {
                            "name": "eventName",
                            "type": "string"
                        },
                        {
                            "name": "timestamp",
                            "type": "string"
                        },
                        {
                            "name": "place",
                            "type": "string"
                        },
                        {
                            "name": "distance",
                            "type": "string"
                        },
                        {
                            "name": "time",
                            "type": "string"
                        },
                        {
                            "name": "description",
                            "type": "string"
                        }
                    ],
                    "name": "createRunStat",
                    "outputs": [],
                    "payable": false,
                    "stateMutability": "nonpayable",
                    "type": "function"
                },
                {
                    "constant": true,
                    "inputs": [],
                    "name": "getRunStats",
                    "outputs": [
                        {
                            "name": "",
                            "type": "string"
                        }
                    ],
                    "payable": false,
                    "stateMutability": "view",
                    "type": "function"
                }
            ],
        );
        // Grab the contract at specified deployed address with the interface defined by the ABI
        sportsStat = SportsStat.at('0xb2ed01b19ef74d92ef19edcb4c8a4d0bda290e6c');
    })();

    // Onclick handlers
    const createSportsStat = async () => {
        web3.eth.getAccounts(async (error, accounts) => {
            if (error) {
                return
            }
            var account = accounts[0];

            const eventName = document.querySelector('#eventName').value;
            const timestamp = document.querySelector('#timestamp').value;
            const place = document.querySelector('#place').value;
            const distance = document.querySelector('#distance').value;
            const time = document.querySelector('#time').value;
            const description = document.querySelector('#description').value;

            if(!eventName || !timestamp || !place || !distance || !time || !description) {
                document.querySelector('#create-stat-error').innerText = 'All fields are required';
                return;
            }

            sportsStat.createRunStat(eventName, timestamp, place, distance, time, description, (error, result) => {
                if(error) {
                    document.querySelector('#create-stat-error').innerText = `Contract failed: ${error.message}`;
                }
                //  Use ajax to call creation of DB elements so we have a local copy
            });
        });
    };

    const statsLookupButtonClicked = async () => {
        web3.eth.getAccounts(async (error, accounts) => {
            if (error) {
                return
            }
            var account = accounts[0];
            const tx = sportsStat.getRunStats((error, result) => {
                if (error) {
                    document.querySelector('#lookup-stats-error').innerText = 'Error: ' + error.message;
                    return;
                }
                document.querySelector('#lookup-stats-result').innerText = result;
            });
        });
    }
</script>
@endpush
