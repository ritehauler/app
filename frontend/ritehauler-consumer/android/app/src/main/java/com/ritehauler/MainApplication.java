package com.ritehauler;

import android.app.Application;

import com.facebook.CallbackManager;
import com.facebook.react.ReactApplication;
import com.bugsnag.BugsnagReactNative;
import com.reactlibrary.RNThumbnailPackage;
import fr.bamlab.rnimageresizer.ImageResizerPackage;
import com.facebook.react.ReactNativeHost;
import com.facebook.react.ReactPackage;
import com.facebook.react.shell.MainReactPackage;
import com.facebook.soloader.SoLoader;
import com.BV.LinearGradient.LinearGradientPackage;
import com.airbnb.android.react.maps.MapsPackage;
import com.imagepicker.ImagePickerPackage;
import com.opensettings.OpenSettingsPackage;
import com.gettipsi.stripe.StripeReactPackage;
import org.devio.rn.splashscreen.SplashScreenReactPackage;
import com.facebook.reactnative.androidsdk.FBSDKPackage;
import io.invertase.firebase.RNFirebasePackage;
import io.invertase.firebase.notifications.RNFirebaseNotificationsPackage;
import io.invertase.firebase.messaging.RNFirebaseMessagingPackage;
import com.facebook.FacebookSdk;
import com.facebook.appevents.AppEventsLogger;
import com.rngrp.RNGRPPackage;
import com.devfd.RNGeocoder.RNGeocoderPackage;
import com.react.rnspinkit.RNSpinkitPackage; 
import android.support.multidex.MultiDexApplication;
import java.util.Arrays;
import java.util.List;

public class MainApplication extends MultiDexApplication implements ReactApplication {

  private static CallbackManager mCallbackManager = CallbackManager.Factory.create();

  protected static CallbackManager getCallbackManager() {
    return mCallbackManager;
  }

  private final ReactNativeHost mReactNativeHost = new ReactNativeHost(this) {
    @Override
    public boolean getUseDeveloperSupport() {
      return BuildConfig.DEBUG;
    }

    @Override
    protected List<ReactPackage> getPackages() {
      return Arrays.<ReactPackage>asList(
          new MainReactPackage(),
          BugsnagReactNative.getPackage(),
          new RNThumbnailPackage(),
          new ImageResizerPackage(),
          new LinearGradientPackage(),
          new MapsPackage(),
          new ImagePickerPackage(),
          new RNGRPPackage(),
          new OpenSettingsPackage(),
          new StripeReactPackage(),
          new FBSDKPackage(mCallbackManager),
          new SplashScreenReactPackage(),
          new GPSTrackerPackage() ,
          new RNGeocoderPackage(),
          new RNSpinkitPackage(),
          new RNFirebasePackage(),
          new RNFirebaseNotificationsPackage(),
          new RNFirebaseMessagingPackage()
      );
    }

    @Override
    protected String getJSMainModuleName() {
      return "index";
    }
  };

  @Override
  public ReactNativeHost getReactNativeHost() {
    return mReactNativeHost;
  }

  @Override
  public void onCreate() {
    super.onCreate();
    SoLoader.init(this, /* native exopackage */ false);
    FacebookSdk.sdkInitialize(getApplicationContext());
    BugsnagReactNative.start(this);
    // If you want to use AppEventsLogger to log events.
    AppEventsLogger.activateApp(this);
    
  }
}
