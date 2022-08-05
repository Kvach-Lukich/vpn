<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Active: {{ $data['active_subscription'] }}
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    Invite code: {{ $data['invite_token'] }}
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    your balance: {{ $data['balance'] }} <br />
                    minimum payment amount: {{ $data['minpay'] }} eur  <br />
                    1 eur = {{ $data['eur1'] }} {{ $data['cur_currency'] }}
                    <form method="POST" id="currency_form" action="{{ route('dashboard') }}">
                        @csrf
                        <select name="currency" id="currency">
                            @foreach ($data['currencies'] as $currency)
                                <option @if($currency==$data['cur_currency']) selected="selected" @endif> {{ $currency }} </option>
                            @endforeach
                        </select>
                    </form>
                    <form method="POST" action="{{ route('charge') }}">
                        @csrf
<!--                        <input type="text" name="wallet" style="width: 500px" >-->
                        <input id="pay" type="number" step="0.01" name="pay" value="{{ $data['pay'] }}" min="{{ $data['minpay'] }}"> EUR = <span id="coin_price">{{ $data['EstimatePrice'] }}</span>{{ $data['cur_currency'] }}
                        <input type="hidden" name="currency" value="{{ $data['cur_currency'] }}">
                        <x-button>
                            {{ __('charge') }}
                        </x-button>
                    </form>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    remains: {{ $data['remains'] }}
                </div>
                @if($data['trial']<1 && !$data['active_subscription'])
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" id="currency_form" action="{{ route('dashboard') }}">
                    @csrf
                        <input type="hidden" name="trial" value="1">
                        <x-button>
                            {{ __('Activete trial') }} 
                        </x-button>24 hors
                    </form>
                </div>
                @endif
                
                @if($data['active_subscription'])
                <div class="p-6 bg-white border-b border-gray-200">
                    <a style="color: #1231CC" href="config">Download config</a>
                </div>
                <div class="p-6 bg-white border-b border-gray-200">
                    <img src="/qrconfig"/>
                </div>
                @endif
                
                @if($data['pol_transaction'])
                <div class="p-6 bg-white border-b border-gray-200">
                    Transaction status: <span style="font-weight: bold;" id="tstat"></span>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
    
<script>
    document.getElementById('currency').oninput = function(){ document.getElementById('currency_form').submit(); };
    
    document.getElementById('pay').oninput = function(){ document.getElementById('coin_price').innerHTML=this.value*{{ $data['eur1'] }} };
    
    @if($data['pol_transaction'])
    var obj=document.getElementById('tstat');
    var timerId = setInterval(function () {
        let xhr = new XMLHttpRequest();
        xhr.open('GET', 'transactionstatus');
        xhr.send();
        xhr.onload = function() {
            obj.innerHTML=xhr.response;
            if(xhr.response=='finished'){
                clearInterval(timerId);
                document.location.href = 'dashboard';
            }
        }
        
    }, 2000);
    @endif
</script>
