var btc       = {{ api[0] }};
var eth       = {{ api[1] }};
var pct       = {{ api[2] }};
var btc_vol   = {{ api[3] }};
var eth_vol   = {{ api[4] }};
var pct_vol   = {{ api[5] }};
var eth_price = {{ api[6] }};
var btc_rwd   = {{ api[7] }};
var eth_rwd   = {{ api[8] }};
var pct_rwd   = {{ api[9] }};
var btc_tx    = {{ api[10] }};
var btc_nodes = {{ api[11] }};

$('#target').html( "$" + btc.toLocaleString('en-US') );