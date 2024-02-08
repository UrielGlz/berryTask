/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$('.daterange').daterangepicker({
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  },
  startDate: moment().startOf('month'),
  endDate: moment().endOf('month')
}, function (start, end) {
  _salesChart(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),);
});

function _salesChart(startDate, endDate) {
  $.post('/Controller/SalesRecord.php', {
    action: 'ajax',
    request: 'getTotalSales',
    start: startDate,
    end: endDate
  }, function (data) {
    $("#canvas_chart_sales").remove();
    $("#chart_sales").append('<canvas id="canvas_chart_sales"></canvas>');

    if (data.response) {
      $("#tbl_sales").find('tbody').empty().append(data.salesData);
      $("#tbl_sales").find('caption').empty().append(data.caption);
      var stores = [];
      var sales = [];
      for (var name in data.chartData) {
        stores.push(name);
        sales.push(data.chartData[name]);
      }
      var barChartData = {
        labels: stores,
        datasets: [{
          label: 'Ventas',
          backgroundColor: '#3b83bd',
          borderColor: '#3b83bd',
          borderWidth: 1,
          data: sales
        }]
      };

      var ctx = document.getElementById('canvas_chart_sales').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: ['Ventas por sucursal', data.caption, 'Total ventas $' + data.totalSales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  if (parseInt(value) >= 1000) {
                    return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return '$' + value;
                  }
                }
              }
            }]
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return "$" + tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              }
            }
          }
        }
      });
    } else {
      $("#tbl_sales").find('tbody').empty().append("<td colspan='2' class='text-center' style='padding:10px'>No se encontraton resultados.</td>");
    }
  }, 'json');
}
//UG Trafico Chard

$('.daterangeTrafico').daterangepicker({
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  },
  startDate: moment().startOf('month'),
  endDate: moment().endOf('month')
}, function (start, end) {
  _TraficoChart(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),);
});
function _TraficoChart(startDate, endDate) {
  $.post('/Controller/SalesRecord.php', {
    action: 'ajax',
    request: 'getTrafico',
    start: startDate,
    end: endDate,
    group: 'HOUR(_ventas.creado_fecha)'
  }, function (data) {
    $("#canvas_chart_trafico").remove();
    $("#chart_trafico").append('<canvas id="canvas_chart_trafico"></canvas>');

    if (data.response) {

      var stores = [];
      var sales = [];
      for (var name in data.chartData) {
        stores.push(name);
        sales.push(data.chartData[name]);
      }
      var barChartData = {
        labels: stores,
        datasets: [{
          label: 'Trafico',
          backgroundColor: '#3b83bd',
          borderColor: '#3b83bd',
          borderWidth: 1,
          data: sales
        }]
      };

      var ctx = document.getElementById('canvas_chart_trafico').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            //text: ['Trafico por sucursal',data.caption,'Total ventas $'+data.totalSales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]
            text: ['Trafico por sucursal', data.caption]
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  if (parseInt(value) >= 1000) {
                    return 'Qty ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return 'Qty ' + value;
                  }
                }
              }
            }]
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return "Qty " + tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              }
            }
          }
        }
      });
    } 
  }, 'json');
}
//UG MERMAS
$('.daterangeMermas').daterangepicker({
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  },
  startDate: moment().startOf('month'),
  endDate: moment().endOf('month')
}, function (start, end) {
  _MermasChart(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),);
});
function _MermasChart(startDate, endDate) {
  $.post('/Controller/SalesRecord.php', {
    action: 'ajax',
    request: 'getMermas_s',
    start: startDate,
    end: endDate,
    group: 'idSucursal'
  }, function (data) {
    $("#canvas_chart_mermas").remove();
    $("#chart_mermas").append('<canvas id="canvas_chart_mermas"></canvas>');

    if (data.response) {

      var stores = [];
      var sales = [];
      for (var name in data.chartData) {
        stores.push(name);
        sales.push(data.chartData[name]);
      }
      var barChartData = {
        labels: stores,
        datasets: [{
          label: 'Mermas',
          backgroundColor: '#e63900',
          borderColor: '#e63900',
          borderWidth: 1,
          data: sales
        }]
      };

      var ctx = document.getElementById('canvas_chart_mermas').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            //text: ['Trafico por sucursal',data.caption,'Total ventas $'+data.totalSales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]
            text: ['Mermas por sucursal', data.caption]
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  if (parseInt(value) >= 1000) {
                    return 'Qty ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return 'Qty ' + value;
                  }
                }
              }
            }]
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return "Qty " + tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              }
            }
          }
        }
      });
    } 
  }, 'json');
}
$('.daterange_special_orders').daterangepicker({
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  },
  startDate: moment().startOf('month'),
  endDate: moment().endOf('month')
}, function (start, end) {
  _specialOrdersChart(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),);
});

function _specialOrdersChart(startDate, endDate) {
  $.post('/Controller/SpecialOrder.php', {
    action: 'ajax',
    request: 'getTotalSales',
    start: startDate,
    end: endDate
  }, function (data) {
    $("#canvas_chart_special_orders").remove();
    $("#chart_special_orders").append('<canvas id="canvas_chart_special_orders"></canvas>');

    if (data.response) {
      $("#tbl_special_orders").find('tbody').empty().append(data.salesData);
      $("#tbl_special_orders").find('caption').empty().append(data.caption);

      var stores = [];
      var sales = [];
      var orders = [];
      for (var name in data.chartDataSales) {
        stores.push(name);
        sales.push(data.chartDataSales[name]);
      }
      for (var name in data.chartDataOrders) {
        orders.push(data.chartDataOrders[name]);
      }
      var barChartData = {
        labels: stores,
        datasets: [{
          type: 'line',
          fill: false,
          label: 'Ventas',
          backgroundColor: '#3b83bd',
          borderColor: '#3b83bd',
          borderWidth: 1,
          yAxisID: 'y-axis-1',
          data: sales
        }, {
          label: 'Pedidos',
          backgroundColor: '#e8b923',
          borderColor: '#e8b923',
          borderWidth: 1,
          yAxisID: 'y-axis-2',
          data: orders
        }]
      };

      var ctx = document.getElementById('canvas_chart_special_orders').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: ['Pedidos especiales por sucursal', data.caption, 'Total pedidos ' + data.totalOrders.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","), 'Total ventas $' + data.totalSales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]
          },
          scales: {
            yAxes: [{
              ticks: {
                min: 0,
                callback: function (value, index, values) {
                  if (parseInt(value) >= 1000) {
                    return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return '$' + value;
                  }
                }
              },
              type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
              display: true,
              position: 'left',
              id: 'y-axis-1'
            }, {
              ticks: {
                min: 0
              },
              type: 'linear', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
              display: true,
              position: 'right',
              id: 'y-axis-2',
              gridLines: {
                drawOnChartArea: false
              }
            }]

          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              }
            }
          }
        }
      });
    } else {
      $("#tbl_special_orders").find('tbody').empty().append("<td colspan='3' class='text-center' style='padding:10px'>No se encontraton resultados.</td>");
    }
  }, 'json');
}


$('.daterangeSalesByStoreId').daterangepicker({
  ranges: {
    'Today': [moment(), moment()],
    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
    'This Month': [moment().startOf('month'), moment().endOf('month')],
    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
  },
  startDate: moment().startOf('month'),
  endDate: moment().endOf('month')
}, function (start, end) {
  _salesChartByStoreId(start.format('MM/DD/YYYY'), end.format('MM/DD/YYYY'),);
});

function _salesChartByStoreId(startDate, endDate) {
  $.post('/Controller/SalesRecord.php', {
    action: 'ajax',
    request: 'getTotalSalesByStoreId',
    start: startDate,
    end: endDate
  }, function (data) {
    $("#canvas_chart_sales").remove();
    $("#chart_sales").append('<canvas id="canvas_chart_sales"></canvas>');

    if (data.response) {
      $("#tbl_sales_by_storeid").find('tbody').empty().append(data.salesData);
      $("#tbl_sales_by_storeid").find('caption').empty().append(data.caption);
      var stores = [];
      var sales = [];
      for (var name in data.chartData) {
        stores.push(name);
        sales.push(data.chartData[name]);
      }
      var barChartData = {
        labels: stores,
        datasets: [{
          label: 'Ventas',
          backgroundColor: '#3b83bd',
          borderColor: '#3b83bd',
          borderWidth: 1,
          data: sales
        }]
      };

      var ctx = document.getElementById('canvas_chart_sales').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top'
          },
          title: {
            display: true,
            text: [data.caption, 'Total ventas $' + data.totalSales.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")]
          },
          scales: {
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function (value, index, values) {
                  if (parseInt(value) >= 1000) {
                    return '$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                  } else {
                    return '$' + value;
                  }
                }
              }
            }]
          },
          tooltips: {
            callbacks: {
              label: function (tooltipItems, data) {
                return "$" + tooltipItems.yLabel.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
              }
            }
          }
        }
      });
    } else {
      $("#tbl_sales").find('tbody').empty().append("<td colspan='2' class='text-center' style='padding:10px'>No se encontraton resultados.</td>");
    }
  }, 'json');
}