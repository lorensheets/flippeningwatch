var eth,
btc,
pct,
eth_vol,
btc_vol,
pct_vol,
btc_tx,
eth_tx = 200419, // 24 hour ETH transaction volume
pct_tx,
btcNodes,
ethNodes = 26841, // ETH Nodes
pctNodes,
eth_price,
btc_rwd,
eth_rwd,
btc_trends = 80, // BTC google trends
eth_trends = 26, // ETH google trends
pct_trends;

function flippeningGetData() {

  $.getJSON( "https://api.coinmarketcap.com/v1/ticker/", function( data ) {
    btc = parseInt( data[0].market_cap_usd );
    eth = parseInt( data[1].market_cap_usd );
    pct = (eth / btc) * 100;
    $("#btc_mkt_cap").html( "$" + btc.toLocaleString('en-US')  );
    $("#eth_mkt_cap").html( "$" + eth.toLocaleString('en-US')  );
    $("#eth_btc_pct").html( pct.toFixed(1) + "%" );
    if (pct > 100) {
      $("#eth_btc_pct").parent().css({
        'background': '#13ff00',
        'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
        'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
      });
      $("#eth_btc_pct").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
    }

    btc_vol = parseInt( data[0]["24h_volume_usd"] );
    eth_vol = parseInt( data[1]["24h_volume_usd"] );
    pct_vol = (eth_vol / btc_vol) * 100;
    $("#btc_vol").html( "$" + btc_vol.toLocaleString('en-US')  );
    $("#eth_vol").html( "$" + eth_vol.toLocaleString('en-US')  );
    $("#pct_vol").html( pct_vol.toFixed(1) + "%" );
    if (pct_vol > 100) {
      $("#pct_vol").parent().css({
        'background': '#13ff00',
        'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
        'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
      });
      $("#pct_vol").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
    }

    eth_price = parseInt ( data[1].price_usd );
    btc_rwd = parseInt( data[0].price_usd ) * 1800;
    $("#btc_rwd").html( "$" + btc_rwd.toLocaleString('en-US') );

    $.getJSON( "https://etherchain.org/api/miningEstimator", function ( data ) {
      eth_rwd = 86400 / parseInt( data.data[0].blockTime ) * 5 * eth_price;
      pct_rwd = (eth_rwd / btc_rwd) * 100;
      $("#eth_rwd").html( "$" + eth_rwd.toLocaleString('en-US') );
      $("#pct_rwd").html( pct_rwd.toFixed(1) + "%" );
      if (pct_rwd > 100) {
        $("#pct_rwd").parent().css({
          'background': '#13ff00',
          'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
          'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
          'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
          'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
        });
        $("#pct_rwd").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
      }
    });

    pct_trends = (eth_trends/btc_trends) * 100;
    $("#btc_trends").html( btc_trends + "*" );
    $("#eth_trends").html( eth_trends + "*" );
    $("#pct_trends").html( pct_trends.toFixed(1) + "%" );
    if (pct_trends > 100) {
      $("#pct_trends").parent().css({
        'background': '#13ff00',
        'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
        'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
      });
      $("#pct_trends").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
    }

  });

  $.ajax({
    url: "https://blockchain.info/q/24hrtransactioncount?&cors=true",
    type: "GET",
    crossDomain: true,
    success: function( data ) {
      btc_tx = parseInt( data );
      pct_tx = (eth_tx / btc_tx) * 100;
      $("#btc_tx").html( btc_tx.toLocaleString('en-US') );
      $("#eth_tx").html( eth_tx.toLocaleString('en-US') + "*" );
      $("#pct_tx").html( pct_tx.toFixed(1) + "%" );
      if (pct_tx > 100) {
        $("#pct_tx").parent().css({
          'background': '#13ff00',
          'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
          'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
          'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
          'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
        });
        $("#pct_tx").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
      }
    }
  });

  $.getJSON("https://bitnodes.21.co/api/v1/snapshots/latest/", function( data ) {
    pctNodes = (ethNodes / data.total_nodes) * 100;
    $("#btcNodes").html( data.total_nodes.toLocaleString('en-US') );
    $("#ethNodes").html( ethNodes.toLocaleString('en-US') + "*" );
    $("#pctNodes").html( pctNodes.toFixed(1) + "%" );
    if (pctNodes > 100) {
      $("#pctNodes").parent().css({
        'background': '#13ff00',
        'background': '-webkit-linear-gradient(left top, #03fcfe, #13ff00)',
        'background': '-o-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': '-moz-linear-gradient(bottom right, #03fcfe, #13ff00)',
        'background': 'linear-gradient(to bottom right, #03fcfe, #13ff00)'
      });
      $("#pctNodes").css({ "color": "#fff", "font-size": "1.2em", "font-weight": "800" });
    }
  });



}

flippeningGetData();
