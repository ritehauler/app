package com.ritehauler;


import android.Manifest;
import android.app.Activity;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentSender;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationManager;
import android.net.Uri;
import android.os.Looper;
import android.preference.PreferenceManager;
import android.provider.Settings;
import android.support.annotation.NonNull;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.AlertDialog;
import android.util.Log;
import android.widget.Toast;

import com.facebook.react.bridge.Arguments;
import com.facebook.react.bridge.Callback;
import com.facebook.react.bridge.ReactContext;
import com.facebook.react.bridge.ReactContextBaseJavaModule;
import com.facebook.react.bridge.ReactApplicationContext;
import com.facebook.react.bridge.ReactMethod;
import com.facebook.react.bridge.WritableMap;
import com.facebook.react.bridge.ActivityEventListener;
import com.facebook.react.bridge.Promise;
import com.facebook.react.bridge.WritableMap;
import com.facebook.react.bridge.Arguments;
import com.facebook.react.bridge.ReadableMap;



import com.facebook.react.modules.core.DeviceEventManagerModule;
import com.facebook.react.modules.core.PermissionAwareActivity;
import com.facebook.react.modules.core.PermissionListener;

import com.google.android.gms.common.api.ApiException;
import com.google.android.gms.common.api.ResolvableApiException;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.location.LocationSettingsRequest;
import com.google.android.gms.location.LocationSettingsResponse;
import com.google.android.gms.location.LocationSettingsStatusCodes;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.SettingsClient;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;

import javax.annotation.Nullable;

import java.text.DateFormat;
import java.util.Date;

import static io.invertase.firebase.Utils.sendEvent;


/**
 * Using location settings.
 * <p/>
 * Uses the {@link com.google.android.gms.location.SettingsApi} to ensure that the device's system
 * settings are properly configured for the app's location needs. When making a request to
 * Location services, the device's system settings may be in a state that prevents the app from
 * obtaining the location data that it needs. For example, GPS or Wi-Fi scanning may be switched
 * off. The {@code SettingsApi} makes it possible to determine if a device's system settings are
 * adequate for the location request, and to optionally invoke a dialog that allows the user to
 * enable the necessary settings.
 * <p/>
 * This sample allows the user to request location updates using the ACCESS_FINE_LOCATION setting
 * (as specified in AndroidManifest.xml).
 */

/**
 * Created by shukarullahshah on 14/11/2017.
 */

public class GPSTracker extends ReactContextBaseJavaModule implements ActivityEventListener {

    private static final String MODULE_NAME = GPSTracker.class.getSimpleName();

    /**
     * Code used in requesting runtime permissions.
     */
    private static final int REQUEST_PERMISSIONS_REQUEST_CODE = 34;


   /**
     * The desired interval for location updates on smallest displacement.
     */
    private static final float UPDATE_INTERVAL_IN_SMALLEST_DISPLACEMENT = 0.5f; // 50 meters

    /**
     * The desired interval for location updates. Inexact. Updates may be more or less frequent.
     */
    private static final long UPDATE_INTERVAL_IN_MILLISECONDS = 2000;

    /**
     * The fastest rate for active location updates. Exact. Updates will never be more frequent
     * than this value.
     */
    private static final long FASTEST_UPDATE_INTERVAL_IN_MILLISECONDS =
            UPDATE_INTERVAL_IN_MILLISECONDS / 2;

    /**
     * Constant used in the location settings dialog.
     */
    private static final int REQUEST_CHECK_SETTINGS = 0x1;


    private static final int ENABLE_LOCATION_SERVICES = 1009;

    /**
     * Provides access to the Location Settings API.
     */
    private SettingsClient mSettingsClient;

    /**
     * Represents a geographical location.
     */
    private Location mCurrentLocation;

    /**
     * Stores parameters for requests to the FusedLocationProviderApi.
     */
    private LocationRequest mLocationRequest;

    /**
     * Callback for Location events.
     */
    private LocationCallback mLocationCallback;

    /**
     * Tracks the status of the location updates request. Value changes when the user presses the
     * Start Updates and Stop Updates buttons.
     */
    private Boolean mRequestingLocationUpdates;

    /**
     * Stores the types of location services the client is interested in using. Used for checking
     * settings to determine if the device has optimal location settings.
     */
    private LocationSettingsRequest mLocationSettingsRequest;

    /**
     * Time when the location was updated represented as a String.
     */
    private ReactApplicationContext reactContext;
    private SharedPreferences preferences;

    private Promise promiseCallback;
    private ReadableMap map;

    private FusedLocationProviderClient mFusedLocationClient;

    public GPSTracker(ReactApplicationContext reactContext) {

        super(reactContext);
        this.reactContext = reactContext;
        this.reactContext.addActivityEventListener(this);
        mRequestingLocationUpdates = false;
        preferences = PreferenceManager.getDefaultSharedPreferences(reactContext);
        mSettingsClient = LocationServices.getSettingsClient(reactContext);
        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(reactContext);

        createLocationCallback();
        createLocationRequest();
        buildLocationSettingsRequest();

    }

    /**
     * Sets up the location request. Android has two location request settings:
     * {@code ACCESS_COARSE_LOCATION} and {@code ACCESS_FINE_LOCATION}. These settings control
     * the accuracy of the current location. This sample uses ACCESS_FINE_LOCATION, as defined in
     * the AndroidManifest.xml.
     * <p/>
     * When the ACCESS_FINE_LOCATION setting is specified, combined with a fast update
     * interval (5 seconds), the Fused Location Provider API returns location updates that are
     * accurate to within a few feet.
     * <p/>
     * These settings are appropriate for mapping applications that show real-time location
     * updates.
     */
    private void createLocationRequest() {
        mLocationRequest = new LocationRequest();
        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
       
        mLocationRequest.setInterval(UPDATE_INTERVAL_IN_MILLISECONDS);

        
        mLocationRequest.setFastestInterval(FASTEST_UPDATE_INTERVAL_IN_MILLISECONDS);

    }

    /**
     * Creates a callback for receiving location events.
     */
    private void createLocationCallback() {
        mLocationCallback = new LocationCallback() {
            @Override
            public void onLocationResult(LocationResult locationResult) {
                super.onLocationResult(locationResult);

                Log.i(MODULE_NAME, "Location is updated");
                mCurrentLocation = locationResult.getLastLocation();
                successCallback();
                stopLocationUpdates();
                //mLastUpdateTime = DateFormat.getTimeInstance().format(new Date());
                //emitLocation();


            }
        };
    }

    /**
     * Uses a {@link com.google.android.gms.location.LocationSettingsRequest.Builder} to build
     * a {@link com.google.android.gms.location.LocationSettingsRequest} that is used for checking
     * if a device has the needed location settings.
     */
    private void buildLocationSettingsRequest() {
        LocationSettingsRequest.Builder builder = new LocationSettingsRequest.Builder();
        builder.addLocationRequest(mLocationRequest);
        mLocationSettingsRequest = builder.build();
    }


    /**
     * Requests location updates from the FusedLocationApi. Note: we don't call this unless location
     * runtime permission has been granted.
     */
    private void checkGpsSettings() {

        // Begin by checking if the device has the necessary location settings.
        mSettingsClient.checkLocationSettings(mLocationSettingsRequest)
                .addOnSuccessListener(getCurrentActivity(), new OnSuccessListener<LocationSettingsResponse>() {
                    @Override
                    public void onSuccess(LocationSettingsResponse locationSettingsResponse) {
                        Log.i(MODULE_NAME, "All location settings are satisfied.");
                        startLocationUpdates();
                        //Toast.makeText(getCurrentActivity(), "Success callback", Toast.LENGTH_LONG).show(); 
                        //successCallback();
                    }
                })
                .addOnFailureListener(getCurrentActivity(), new OnFailureListener() {
                    @Override
                    public void onFailure(@NonNull Exception e) {
                       // Toast.makeText(getCurrentActivity(), "Location settings are off", Toast.LENGTH_LONG).show();
                        int statusCode = ((ApiException) e).getStatusCode();
                        switch (statusCode) {
                            case LocationSettingsStatusCodes.RESOLUTION_REQUIRED:
                                Log.i(MODULE_NAME, "Location settings are not satisfied. Attempting to upgrade " +
                                        "location settings ");
                                try {   
                                    // Show the dialog by calling startResolutionForResult(), and check the
                                    // result in onActivityResult().
                                    ResolvableApiException rae = (ResolvableApiException) e;
                                    rae.startResolutionForResult(getCurrentActivity(), REQUEST_CHECK_SETTINGS);
                                } catch (IntentSender.SendIntentException sie) {
                                    Log.i(MODULE_NAME, "PendingIntent unable to execute request.");
                                    //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
                                    failureCallback("permission");
                                }
                                break;
                            case LocationSettingsStatusCodes.SETTINGS_CHANGE_UNAVAILABLE:
                                String errorMessage = "Location settings are inadequate, and cannot be " +
                                        "fixed here. Fix in Settings.";
                                Log.e(MODULE_NAME, errorMessage);
                                failureCallback("unavailable");
                                mRequestingLocationUpdates = false;
                                //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
                                //Toast.makeText(getCurrentActivity(), errorMessage, Toast.LENGTH_LONG).show();
                                
                        }
                    }
                });
    }

    private void startLocationUpdates() {
        
        Log.i(MODULE_NAME, "startLocationUpdates");
        emitLocation();
        mRequestingLocationUpdates = true;

        //noinspection MissingPermission
        mFusedLocationClient.requestLocationUpdates(mLocationRequest,
                mLocationCallback, Looper.myLooper());

    }

    private void emitLocation() {
        Log.i(MODULE_NAME, "emitLocation");
        WritableMap params = Arguments.createMap();
        params.putBoolean("gettingLocation",true);
        sendEvent(reactContext, "gettingLocationNotification", params);
    }


    private void successCallback() {
        if ( promiseCallback == null) return;
        WritableMap result = Arguments.createMap();
        //result.putBoolean("enabled", true);
        result.putDouble("latitude", mCurrentLocation.getLatitude());
        result.putDouble("longitude", mCurrentLocation.getLongitude());
        promiseCallback.resolve(result);
    }

    private void failureCallback(String error) {
        if ( promiseCallback == null) return;
        promiseCallback.reject(new Throwable(error));
    }
   
    /**
     * Return the current state of the permissions needed.
     */
    private boolean checkPermissions() {
        return ActivityCompat.checkSelfPermission(getCurrentActivity(),
                Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED;
    }

    private boolean isPermissionDenied() {
        return ActivityCompat.shouldShowRequestPermissionRationale(getCurrentActivity(),
                Manifest.permission.ACCESS_FINE_LOCATION);
    }

    private boolean isPermissionPermanentlyDenied() {
        return preferences.getBoolean("permissionPermanentlyDenied", false);
    }

    private void setPermissionPermanentlyDenied(boolean flag) {
        SharedPreferences.Editor editor = preferences.edit();
        editor.putBoolean("permissionPermanentlyDenied", flag);
        editor.apply();
    }


    private void requestPermissions() {
        final Activity activity = getCurrentActivity();

        String title = map.hasKey("title") ? map.getString("title") : "Location Permission Required";
        String message = map.hasKey("message") ? map.getString("message") : "For the best experience, please enable Permission -> Location in the application settings.";
        String buttonText = map.hasKey("buttonText") ? map.getString("buttonText") : "GO TO SETTINGS";

        // Provide an additional rationale to the user. This would happen if the user denied the
        // request previously, but didn't check the "Don't ask again" checkbox.
        if (isPermissionPermanentlyDenied()) {
            Log.i(MODULE_NAME, "Displaying permission rationale to provide additional context.");
            AlertDialog.Builder builder = new AlertDialog.Builder(getCurrentActivity());
            builder.setOnCancelListener(new DialogInterface.OnCancelListener() {
                @Override
                public void onCancel(DialogInterface dialog) {
                    failureCallback("permanent");
                    // dialog dismiss without button press
                }
            });
            builder.setTitle(title)
                    .setMessage(message)
                    .setPositiveButton(buttonText, new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) {
                            Intent intent = new Intent();
                            intent.setAction(android.provider.Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
                            Uri uri = Uri.fromParts("package", BuildConfig.APPLICATION_ID, null);
                            intent.setData(uri);
                            getCurrentActivity().startActivityForResult(intent, ENABLE_LOCATION_SERVICES);
                        }
                    })
                    .setCancelable(true)
                    .show();
        } else {
            Log.i(MODULE_NAME, "Requesting permission");
            // Request permission. It's possible this can be auto answered if device policy
            // sets the permission in a given state or the user denied the permission
            // previously and checked "Never ask again".

            ((PermissionAwareActivity) activity).requestPermissions(new String[]{Manifest.permission.ACCESS_FINE_LOCATION}, REQUEST_PERMISSIONS_REQUEST_CODE, new PermissionListener() {
                /**
                 * Callback received when a permissions request has been completed.
                 */
                @Override
                public boolean onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
                    Log.i(MODULE_NAME, "onRequestPermissionResult");
                    if (requestCode == REQUEST_PERMISSIONS_REQUEST_CODE) {
                        if (grantResults.length > 0
                        && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                            checkGpsSettings();
                                if (isPermissionPermanentlyDenied()) {
                                    setPermissionPermanentlyDenied(false);
                                }

                        } else if (!isPermissionDenied()) {
                            setPermissionPermanentlyDenied(true);
                            failureCallback("permanent");
                            //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
                        } else {
                            failureCallback("permission");
                            //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
                        }
                        

                    }
                    return false;
                }
            });
        }
    }

    /**
     * Removes location updates from the FusedLocationApi.
     */
    private void stopLocationUpdates() {
        if (!mRequestingLocationUpdates) {
            Log.d(MODULE_NAME, "stopLocationUpdates: updates never requested, no-op.");
            return;
        }

        // It is a good practice to remove location requests when the activity is in a paused or
        // stopped state. Doing so helps battery performance and is especially
        // recommended in applications that request frequent location updates.
        mFusedLocationClient.removeLocationUpdates(mLocationCallback)
                .addOnCompleteListener(getCurrentActivity(), new OnCompleteListener<Void>() {
                    @Override
                    public void onComplete(@NonNull Task<Void> task) {
                        mRequestingLocationUpdates = false;
                    }
                });
    }

    private boolean checkGpsSettingsAreOn() {
        LocationManager manager = (LocationManager) getCurrentActivity().getSystemService(Context.LOCATION_SERVICE );
        boolean statusOfGPS = manager.isProviderEnabled(LocationManager.GPS_PROVIDER);
        return statusOfGPS;
    }

    /*
    @ReactMethod
    public void onResume() {
        if (checkPermissions() && checkGpsSettingsAreOn()) {
            startLocationUpdates();
        } else if (!checkPermissions()) {
            requestPermissions();
        }
    }
    */

    /*
    @ReactMethod
    protected void onPause() {
        stopLocationUpdates();
    }
    */

    @ReactMethod
    public void getCurrentPosition(ReadableMap configMap,Promise promise) {
        map = configMap;
        promiseCallback = promise;
        
        /*
        if(checkPermissions() && checkGpsSettingsAreOn() && mCurrentLocation!=null) {
            successCallback();
        }
        */

        if (checkPermissions()) {
            checkGpsSettings();
        } else if (!checkPermissions()) {
            requestPermissions();
        }
    }

    @Override
    public String getName() {
        return MODULE_NAME;
    }

    @Override
    public void onActivityResult(Activity activity, int requestCode, int resultCode, Intent data) {

        if (requestCode == REQUEST_CHECK_SETTINGS) {
            if (resultCode == Activity.RESULT_OK) {
                //successCallback();
                startLocationUpdates();
                //Toast.makeText(getCurrentActivity(), "Success callback", Toast.LENGTH_LONG).show();
            }  else {
                failureCallback("permanent");
                //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
            }

        } else if (requestCode == ENABLE_LOCATION_SERVICES) {
            if (checkPermissions()) {
                checkGpsSettings();
            } else {
                failureCallback("gps");
                //Toast.makeText(getCurrentActivity(), "failure callback", Toast.LENGTH_LONG).show();
            }
        }
    }

    @Override
    public void onNewIntent(Intent intent) {
    }
}
