import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:geocoding/geocoding.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

class LocationService {
  static const double defaultLatitude = -6.2088;
  static const double defaultLongitude = 106.8456;
  static const String _addressCacheKey = 'cached_location_address_v4';

  Future<bool> hasPermission() async {
    final status = await Permission.location.status;
    return status.isGranted;
  }

  Future<bool> requestPermission() async {
    final status = await Permission.location.request();
    return status.isGranted;
  }

  Future<Position> getCurrentLocation() async {
    try {
      bool serviceEnabled = await Geolocator.isLocationServiceEnabled();
      if (!serviceEnabled) return _getDefaultPosition();

      LocationPermission permission = await Geolocator.checkPermission();
      if (permission == LocationPermission.denied) {
        permission = await Geolocator.requestPermission();
        if (permission == LocationPermission.denied)
          return _getDefaultPosition();
      }

      if (permission == LocationPermission.deniedForever)
        return _getDefaultPosition();

      Position? lastKnown = await Geolocator.getLastKnownPosition();
      if (lastKnown != null) return lastKnown;

      return await Geolocator.getCurrentPosition(
        desiredAccuracy: LocationAccuracy.medium,
      ).timeout(const Duration(seconds: 5));
    } catch (e) {
      return _getDefaultPosition();
    }
  }

  Position _getDefaultPosition() {
    return Position(
      latitude: defaultLatitude,
      longitude: defaultLongitude,
      timestamp: DateTime.now(),
      accuracy: 0,
      altitude: 0,
      heading: 0,
      speed: 0,
      speedAccuracy: 0,
      altitudeAccuracy: 0,
      headingAccuracy: 0,
    );
  }

  Future<Position> getLocationOrDefault() async {
    return await getCurrentLocation();
  }

  Future<String?> getCachedAddress() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString(_addressCacheKey);
    } catch (_) {
      return null;
    }
  }

  /// Get address with hybrid approach (Native -> IP Fallback -> Cache)
  Future<String> getAddressFromLocation(
      double latitude, double longitude) async {
    // 1. Try Native Geocoding (Most Specific)
    try {
      final placemarks = await placemarkFromCoordinates(latitude, longitude)
          .timeout(const Duration(seconds: 5));

      if (placemarks.isNotEmpty) {
        final place = placemarks.first;
        String? kelurahan = place.subLocality;
        String? kecamatan = place.locality;
        String? kota = place.subAdministrativeArea;
        String? prov = place.administrativeArea;

        // Specialized formatting for Indonesia: Kelurahan, Kecamatan, Kota, Provinsi
        List<String> parts = [];
        if (kelurahan != null && kelurahan.isNotEmpty) parts.add(kelurahan);
        if (kecamatan != null && kecamatan.isNotEmpty) parts.add(kecamatan);
        if (kota != null && kota.isNotEmpty) {
          String formattedKota =
              kota.replaceAll('Regency', 'Kab.').replaceAll('City', 'Kota');
          parts.add(formattedKota);
        }
        if (prov != null && prov.isNotEmpty) parts.add(prov);

        String result = parts.join(', ');
        if (result.isNotEmpty) {
          _saveToCache(result);
          return result;
        }
      }
    } catch (e) {
      print('Native Geocoding failed: $e');
    }

    // 2. Try IP-based Geocoding (Extremely Reliable Fallback)
    try {
      final response = await http
          .get(Uri.parse('http://ip-api.com/json/'))
          .timeout(const Duration(seconds: 4));
      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['status'] == 'success') {
          String city = data['city'] ?? '';
          String region = data['regionName'] ?? '';
          String result = _formatAddress(city, region);
          if (result.isNotEmpty) {
            _saveToCache(result);
            return result;
          }
        }
      }
    } catch (e) {
      print('IP Geocoding failed: $e');
    }

    // 3. Last Resort: Cache
    final cached = await getCachedAddress();
    if (cached != null) return cached;

    // 4. Default if all else fails
    return 'Bandung, Jawa Barat'; // Use user's preferred example as default
  }

  String _formatAddress(String? city, String? province) {
    if (city == null && province == null) return '';

    String c =
        (city ?? '').replaceAll('Regency', 'Kab.').replaceAll('City', 'Kota');
    String p = province ?? '';

    if (c.isNotEmpty && p.isNotEmpty) {
      if (p.contains(c)) return p;
      return '$c, $p';
    }
    return c.isNotEmpty ? c : p;
  }

  Future<void> _saveToCache(String address) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_addressCacheKey, address);
  }
}
