var mapObj = new AMap.Map('container',{
    resizeEnable: true,//是否启用调整
    zoom: 10,//变焦
    center: [116.480983, 40.0958]//中心
});


mapObj.plugin(['AMap.ToolBar','AMap.AdvancedInfoWindow'],function(){
    //创建并添加工具条控件
    var toolBar = new AMap.ToolBar();
    mapObj.addControl(toolBar);

})

function  getUserPositionInfo(){
    var str="需要获取您的位置信息";
    if (confirm(str)) {
        //获取用户位置
        mapObj.plugin('AMap.Geolocation', function () {
            geolocation = new AMap.Geolocation({
                enableHighAccuracy: true,//是否使用高精度定位，默认:true
                timeout: 5000,          //超过10秒后停止定位，默认：无穷大
                maximumAge: 0,           //定位结果缓存0毫秒，默认：0
                convert: true,           //自动偏移坐标，偏移后的坐标为高德坐标，默认：true
                showButton: true,        //显示定位按钮，默认：true
                buttonPosition: 'LB',    //定位按钮停靠位置，默认：'LB'，左下角
                buttonOffset: new AMap.Pixel(10, 20),//定位按钮与设置的停靠位置的偏移量，默认：Pixel(10, 20)
                showMarker: true,        //定位成功后在定位到的位置显示点标记，默认：true
                showCircle: true,        //定位成功后用圆圈表示定位精度范围，默认：true
                panToLocation: true,     //定位成功后将定位到的位置作为地图中心点，默认：true
                zoomToAccuracy:true      //定位成功后调整地图视野范围使定位位置及精度范围视野内可见，默认：false
            });
            mapObj.addControl(geolocation);

            geolocation.getCurrentPosition(function(status,result){
                alert(result.formattedAddress);
                document.getElementById("position_card").innerText=result.formattedAddress;
            })

        });
    }else{
        //alert("你点击取消了位置获取失败")
    }
}