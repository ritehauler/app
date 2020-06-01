package com.ritehaulerdriver;


import android.Manifest;
import android.app.Activity;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentSender;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.location.Location;
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
import com.google.android.gms.location.SettingsClient;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnFailureListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;

import javax.annotation.Nullable;

import java.text.DateFormat;
import java.util.Date;


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

public class GPSTracker extends ReactContextBaseJavaModule {

    private static final String MODULE_NAME = GPSTracker.class.getSimpleName();

    /**
     * Code used in requesting runtime permissions.
     */
    private static final int REQUEST_PERMISSIONS_REQUEST_CODE = 34;

    /**
     * Constant used in the location settings dialog.
     */
    private static final int REQUEST_CHECK_SETTINGS = 0x1;

    /**
     * The desired interval for location updates on smallest displacement.
     */
    private static final float UPDATE_INTERVAL_IN_SMALLEST_DISPLACEMENT = 1f; // 100 meters


    /**
     * The desired interval for location updates. Inexact. Updates may be more or less frequent.
     */
    private static final long UPDATE_INTERVAL_IN_MILLISECONDS = 10000;

    /**
     * The fastest rate for active location updates. Exact. Updates will never be more frequent
     * than this value.
     */
    private static final long FASTEST_UPDATE_INTERVAL_IN_MILLISECONDS =
            UPDATE_INTERVAL_IN_MILLISECONDS / 2;


    /**
     * Provides access to the Fused Location Provider API.
     */
    private FusedLocationProviderClient mFusedLocationClient;

    /**
     * Provides access to the Location Settings API.
     */
    private SettingsClient mSettingsClient;

    /**
     * Stores parameters for requests to the FusedLocationProviderApi.
     */
    private LocationRequest mLocationRequest;

    /**
     * Stores the types of location services the client is interested in using. Used for checking
     * settings to determine if the device has optimal location settings.
     */
    private LocationSettingsRequest mLocationSettingsRequest;

    /**
     * Callback for Location events.
     */
    private LocationCallback mLocationCallback;

    /**
     * Represents a geographical location.
     */
    private Location mCurrentLocation;

    /**
     * Tracks the status of the location updates request. Value changes when the user presses the
     * Start Updates and Stop Updates buttons.
     */
    private Boolean mRequestingLocationUpdates;

    /**
     * Time when the location was updated represented as a String.
     */
    private String mLastUpdateTime;

    private boolean permissionsForeverDenied;

    private ReactApplicationContext reactContext;

    private SharedPreferences preferences;

    public GPSTracker(ReactApplicationContext reactContext) {
        super(reactContext);

        this.reactContext = reactContext;

        mRequestingLocationUpdates = false;
        mLastUpdateTime = "";

        preferences = PreferenceManager.getDefaultSharedPreferences(reactContext);

        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(reactContext);
        mSettingsClient = LocationServices.getSettingsClient(reactContext);

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

        // Sets the desired interval for active location updates. This interval is
        // inexact. You may not receive updates at all if no location sources are available, or
        // you may receive them slower than requested. You may also receive updates faster than
        // requested if other applications are requesting location at a faster interval.
        mLocationRequest.setInterval(UPDATE_INTERVAL_IN_MILLISECONDS);

        // Sets the fastest rate for active location updates. This interval is exact, and your
        // application will never receive updates faster than this value.
        mLocationRequest.setFastestInterval(FASTEST_UPDATE_INTERVAL_IN_MILLISECONDS);

        mLocationRequest.setSmallestDisplacement(UPDATE_INTERVAL_IN_SMALLEST_DISPLACEMENT);

        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
    }

    /**
     * Creates a callback for receiving location events.
     */
    private void createLocationCallback() {
        mLocationCallback = new LocationCallback() {
            @Override
            public void onLocationResult(LocationResult locationResult) {
                super.onLocationResult(locationResult);

                mCurrentLocation = locationResult.getLastLocation();
                mLastUpdateTime = DateFormat.getTimeInstance().format(new Date());
                emitLocation();
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
    private void startLocationUpdates() {
        // Begin by checking if the device has the necessary location settings.
        mSettingsClient.checkLocationSettings(mLocationSettingsRequest)
                .addOnSuccessListener(getCurrentActivity(), new OnSuccessListener<LocationSettingsResponse>() {
                    @Override
                    public void onSuccess(LocationSettingsResponse locationSettingsResponse) {
                        Log.i(MODULE_NAME, "All location settings are satisfied.");

                        //noinspection MissingPermission
                        mFusedLocationClient.requestLocationUpdates(mLocationRequest,
                                mLocationCallback, Looper.myLooper());
                    }
                })
                .addOnFailureListener(getCurrentActivity(), new OnFailureListener() {
                    @Override
                    public void onFailure(@NonNull Exception e) {
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
                                }
                                break;
                            case LocationSettingsStatusCodes.SETTINGS_CHANGE_UNAVAILABLE:
                                String errorMessage = "Location settings are inadequate, and cannot be " +
                                        "fixed here. Fix in Settings.";
                                Log.e(MODULE_NAME, errorMessage);
                                Toast.makeText(getCurrentActivity(), errorMessage, Toast.LENGTH_LONG).show();
                                mRequestingLocationUpdates = false;
                        }
                    }
                });
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

        // Provide an additional rationale to the user. This would happen if the user denied the
        // request previously, but didn't check the "Don't ask again" checkbox.
        if (isPermissionPermanentlyDenied()) {
            Log.i(MODULE_NAME, "Displaying permission rationale to provide additional context.");

            AlertDialog.Builder builder = new AlertDialog.Builder(getCurrentActivity());
            builder.setTitle("Location Permission Required")
                    .setMessage("For the best experience, please enable Permission->Location in the application settings.")
                    .setPositiveButton("GO TO SETTINGS", new DialogInterface.OnClickListener() {
                        public void onClick(DialogInterface dialog, int which) {
                            Intent intent = new Intent();
                            intent.setAction(Settings.ACTION_APPLICATION_DETAILS_SETTINGS);
                            Uri uri = Uri.fromParts("package", BuildConfig.APPLICATION_ID, null);
                            intent.setData(uri);
                            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
                            getCurrentActivity().startActivity(intent);
                        }
                    })
                    .setCancelable(false)
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
                        if (grantResults.length <= 0) {
                            // If user interaction was interrupted, the permission request is cancelled and you
                            // receive empty arrays.
                            Log.i(MODULE_NAME, "User interaction was cancelled.");
                        } else if (grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                            if (mRequestingLocationUpdates) {
                                Log.i(MODULE_NAME, "Permission granted, updates requested, starting location updates");
                                startLocationUpdates();
                                if (isPermissionPermanentlyDenied()) {
                                    setPermissionPermanentlyDenied(false);
                                }
                            }
                        } else if (!isPermissionDenied()) {
                            setPermissionPermanentlyDenied(true);
                        }
                    }

                    //TODO: emit listener for permission accepted, denied or permanently denied

                    return false;
                }
            });
        }
    }

    @ReactMethod
    public void onResume() {
        try {
            if (checkPermissions()) {
                mRequestingLocationUpdates = true;
                startLocationUpdates();
            } else if (!checkPermissions()) {
                requestPermissions();
            }
        } catch (Exception e) {
            Log.d("crash : ", "exception: " + e.toString());
        }
    }

    @ReactMethod
    protected void onPause() {
        try {
            stopLocationUpdates();
        } catch (Exception e) {
            Log.d("crased : ", "exception: " + e.toString());
        }
    }

    @ReactMethod
    public void getCurrentLocation(Callback successCallback) {
        successCallback.invoke(mCurrentLocation.getLatitude(), mCurrentLocation.getLongitude(), mLastUpdateTime);
    }

    private void emitLocation() {
        Log.i(MODULE_NAME, "emitLocation");
        WritableMap params = Arguments.createMap();
        params.putDouble("latitude", mCurrentLocation.getLatitude());
        params.putDouble("longitude", mCurrentLocation.getLongitude());
        params.putString("updateTime", new Date().toString());

        sendEvent(reactContext, "updateLocationNotification", params);
    }


    /**
     * Native modules can signal events to JavaScript without being invoked directly.
     * The easiest way to do this is to use the RCTDeviceEventEmitter
     * which can be obtained from the ReactContext
     *
     * @param reactContext
     * @param eventName
     * @param params
     */
    private void sendEvent(ReactContext reactContext,
                           String eventName,
                           @Nullable WritableMap params) {
        reactContext
                .getJSModule(DeviceEventManagerModule.RCTDeviceEventEmitter.class)
                .emit(eventName, params);
    }


    @Override
    public String getName() {
        return MODULE_NAME;
    }
}
