@extends('layouts.app')

@section('content')
	<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
	
	<style>
        .horizontalweatherForecast {
            width: 45%;
            height:120px;
          
        }
        .verticalweatherForecast {
            width: 45%;
            height:380px;
        }

        .forecastwidget {
            display: flex;
            flex-direction: column;
            padding: 5px;
            font-size: 14px;
            background-color:#ffffff;
            margin:10px;
            border-radius: 7px;
        }
        
        .forecastwidget .days {
            display: flex;
            flex-direction: row;
            justify-content: start;
            flex: 1;
            overflow: hidden;
        }
        
        .forecastwidget .days.vertical {
            flex-direction: column;
            font-size: 1.2em;
        }

        .forecastwidget .days .day {
            display: grid;
			grid-template-columns: 50% 50%;
			
            justify-content: center;
            align-items: center;
            padding: 0px 5px;
            font-size: 0.9em;
            justify-items: center;
            grid-gap: 1px;
        }   

        .forecastwidget .days .day * {
            grid-column: span 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .forecastwidget .days .day .maxt {
            grid-column: 1 ;
        }
        .forecastwidget .days .day .mint {
            grid-column: 2 ;
        }
        
        .forecastwidget .days.vertical .day {
            grid-template-columns: 50px 30px 30px 30px 60px auto;
            grid-gap: 4px;
            justify-content: start;
            margin: 2px 0px;
            padding: 4px 0px;
        }

        .forecastwidget .days.vertical .day * {
            grid-column: span 1;
        }

        .forecastwidget .days.vertical .day .maxt,  .forecastwidget .days.vertical .day .mint  {
            grid-column: default ;
        }
       
        .forecastwidget .location {
            font-size: 1em;
            font-weight: bold;
            flex:0;
        }
        .forecastwidget .footer {
            font-size: 0.7em;
            color:darkgrey;
            display: flex;
            justify-content: flex-end;
        }
        .forecastwidget .days .day .date {
            color:darkgrey;
            font-size: 0.9em;
            white-space: nowrap;
        }


        .forecastwidget .days .day:not(:last-child) {
            border-right:1px solid darkgray;
        }

        .forecastwidget .days.vertical .day:not(:last-child) {
            border-right:none;
            border-bottom:1px solid darkgray;
        }

        .forecastwidget .days .day .conditions {
            display:none;
            color:darkgrey;
            font-size: 0.9em;
        }
        .forecastwidget .days.vertical .day .conditions {
            display:block;
        }
        .forecastwidget .days .day  .maxt {
            font-weight: bold;
        }
        .forecastwidget .days .day  .mint {
            font-size: 0.9em;
            color:darkgrey;
        }
       
        .forecastwidget .days .day  .hidden {
           visibility: hidden;
        }

        .forecastwidget .icon {
            width:40px;
            height:40px;
            background-size: contain;
        }
    
        .forecastwidget .icon.snow {background-image:url("https://www.flaticon.com/svg/vstatic/svg/3026/3026305.svg?token=exp=1620701238~hmac=c3bcf94b22f97d82831cb64a7284fadc");}
        .forecastwidget .icon.rain {background-image:url("https://www.flaticon.com/svg/vstatic/svg/3351/3351962.svg?token=exp=1620701295~hmac=bc7a543f912ed21253ed3db38537bb88");}
        .forecastwidget .icon.fog {background-image:url("https://www.flaticon.com/svg/vstatic/svg/495/495651.svg?token=exp=1620630526~hmac=8abbd01f5f243c5294571ed55dc3bd10");}
        .forecastwidget .icon.wind {background-image:url("https://www.flaticon.com/svg/vstatic/svg/959/959737.svg?token=exp=1620630574~hmac=8846ee54971e0a3910ad77fbc23f682a");}
        .forecastwidget .icon.cloudy {background-image:url("https://www.flaticon.com/svg/vstatic/svg/1146/1146869.svg?token=exp=1620701514~hmac=6d46ef0a33570d70a22ca361bca8adc7");}
        .forecastwidget .icon.partly-cloudy-day {background-image:url("https://www.flaticon.com/svg/vstatic/svg/1146/1146869.svg?token=exp=1620701514~hmac=6d46ef0a33570d70a22ca361bca8adc7");}
        .forecastwidget .icon.partly-cloudy-night {background-image:url("https://www.flaticon.com/svg/vstatic/svg/1146/1146869.svg?token=exp=1620701514~hmac=6d46ef0a33570d70a22ca361bca8adc7");}
        .forecastwidget .icon.clear-day {background-image:url("https://www.flaticon.com/svg/vstatic/svg/72/72643.svg?token=exp=1620701393~hmac=87c273b79d930f13d4977345dba12f80");}
        .forecastwidget .icon.clear-night {background-image:url("https://www.flaticon.com/svg/vstatic/svg/4039/4039918.svg?token=exp=1620701458~hmac=b34cb342acf81790c8c2ea4294a97832");}
    </style>
    <script>
        var WeatherForecastDisplay=(function() {
            var MONTHS=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];

            function WeatherForecastWidget(selector) {
                this.config={
                    "location":selector.attr("data-location"),
                    "unitGroup":selector.attr("data-unitGroup") || "us",
                    "key": selector.attr("data-key") 
                }

                this.selector=selector;

                this.data=null;

                var me=this;

                this.loadForecastData=function() {
                    me.refresh();
                    var uri="https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/weatherdata/forecast?";
                    uri+="unitGroup="+me.config.unitGroup+"&locationMode=single&aggregateHours=24&contentType=json&iconSet=icons1&location="+me.config.location+"&key="+me.config.key;
                    $.get(uri, function( rawResult ) {
                        me.data=rawResult;
                        me.refresh();
                    });
                }

                this.refresh=function() {
                    var root=$(me.selector);

                    if (!me.data) {
                        $(me.selector).html("No data available for "+me.config.location);
                        return;
                    }
                    var locationData=me.data.location;

                    var forecastValues=locationData.values;

                    root.toggleClass("forecastwidget", true);
                    root.html("<div class='location'></div>"+
                                "<div class='days'></div>"+
                                "<div class='footer'><a href='https://www.visualcrossing.com/weather-api' title='Weather Data by Visual Crossing' target='_blank'>Credit</a>");

                    var rect=root.get(0).getBoundingClientRect()
                    
                    var isVertical=rect.height>rect.width;
                    root.children(".days").toggleClass("vertical", isVertical);

                    root.children(".location").html(me.config.location);
                    forecastValues.forEach(function(d) {
                        var dayElement=$("<div class='day'>"+
                                "<div class='date'></div>"+
                                "<div class='icon'></div>"+
                                "<div class='maxt'></div>"+
                                "<div class='mint'></div>"+
                                "<div class='precip'><span class='value'></span></div>"+
                                "<div class='conditions'></div>"+
                                "</div>");
                        
                        root.find(".days").append(dayElement);
                                            
                        dayElement.find(".maxt").html(Math.round(d.maxt));
                        dayElement.find(".mint").html(Math.round(d.mint));
                        dayElement.find(".conditions").html(d.conditions);
                        
                        var date= new Date(d.datetimeStr);
                        
                        dayElement.find(".date").html(MONTHS[date.getMonth()]+" "+date.getDate());

                        var precip=dayElement.find(".precip");
                        precip.toggleClass("hidden",  !d.precip);
                        precip.find(".value").html(d.precip);
                        
                        var icon=dayElement.find(".icon");
                        icon.toggleClass(d.icon,true);
                    });
                }
            }

            var attach=function(selector) {
                var instance=new WeatherForecastWidget($(selector) );
                instance.loadForecastData();
                return instance;
            }
            return {
                "attach":attach
            }
        })();
    </script>
    <div class="text-center text-3xl mt-12 mb-12 font-serif">Weather</div>
    <div class="flex justify-center space-x-4 text-center items-center font-serif">
        <div class="horizontalweatherForecast shadow-md" data-key="HGHKS6AMW73Q7P85YLQ2PLW5K" data-location="Ho Chi Minh, VN" data-unitGroup="metric"></div>
        <div class="verticalweatherForecast shadow-md" data-key="HGHKS6AMW73Q7P85YLQ2PLW5K" data-location="Nha Trang, VN" data-unitGroup="metric"></div>
    </div>
    <script>
        WeatherForecastDisplay.attach(".horizontalweatherForecast");
        WeatherForecastDisplay.attach(".verticalweatherForecast");
    </script>
@endsection