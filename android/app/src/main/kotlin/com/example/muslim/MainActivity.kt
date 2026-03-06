package com.example.muslim

import android.hardware.GeomagneticField
import androidx.annotation.NonNull
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel

class MainActivity: FlutterActivity() {
    private val CHANNEL = "com.example.muslim/compass"

    override fun configureFlutterEngine(@NonNull flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)
        MethodChannel(flutterEngine.dartExecutor.binaryMessenger, CHANNEL).setMethodCallHandler { call, result ->
            if (call.method == "getDeclination") {
                val latitude = call.argument<Double>("latitude")
                val longitude = call.argument<Double>("longitude")
                val altitude = call.argument<Double>("altitude") ?: 0.0
                
                if (latitude != null && longitude != null) {
                    val geoField = GeomagneticField(
                        latitude.toFloat(),
                        longitude.toFloat(),
                        altitude.toFloat(),
                        System.currentTimeMillis()
                    )
                    result.success(geoField.declination.toDouble())
                } else {
                    result.error("INVALID_ARGS", "Latitude and longitude required", null)
                }
            } else {
                result.notImplemented()
            }
        }
    }
}
