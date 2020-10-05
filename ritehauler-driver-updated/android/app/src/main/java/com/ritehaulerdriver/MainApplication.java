package com.ritehaulerdriver;

import android.app.Application;

import com.BV.LinearGradient.LinearGradientPackage;
import com.airbnb.android.react.maps.MapsPackage;
import com.facebook.react.ReactApplication;
import com.reactnativecommunity.geolocation.GeolocationPackage;
import com.reactnativecommunity.netinfo.NetInfoPackage;
import com.reactnativecommunity.viewpager.RNCViewPagerPackage;
import com.swmansion.reanimated.ReanimatedPackage;
import com.swmansion.gesturehandler.react.RNGestureHandlerPackage;
import com.reactnativecommunity.asyncstorage.AsyncStoragePackage;
import com.facebook.react.ReactNativeHost;
import com.facebook.react.ReactPackage;
import com.facebook.react.shell.MainReactPackage;
import com.facebook.soloader.SoLoader;
import com.horcrux.svg.SvgPackage;
import com.imagepicker.ImagePickerPackage;
import com.ocetnik.timer.BackgroundTimerPackage;
import com.splashscreen.SplashScreenPackage;

import java.util.Arrays;
import java.util.List;

import fr.bamlab.rnimageresizer.ImageResizerPackage;
import io.invertase.firebase.RNFirebasePackage;
import io.invertase.firebase.messaging.RNFirebaseMessagingPackage;
import io.invertase.firebase.notifications.RNFirebaseNotificationsPackage;


// fire base
//import io.invertase.firebase.notifications;

public class MainApplication extends Application implements ReactApplication {

  private final ReactNativeHost mReactNativeHost = new ReactNativeHost(this) {
    @Override
    public boolean getUseDeveloperSupport() {
      return BuildConfig.DEBUG;
    }

    @Override
    protected List<ReactPackage> getPackages() {
      return Arrays.<ReactPackage>asList(
            new MainReactPackage(),
            new GeolocationPackage(),
            new NetInfoPackage(),
            new RNCViewPagerPackage(),
            new ReanimatedPackage(),
            new RNGestureHandlerPackage(),
            new AsyncStoragePackage(),
            new RNFirebasePackage(),
            new SvgPackage(),
            new BackgroundTimerPackage(),
            new RNFirebaseNotificationsPackage(),
            new RNFirebaseMessagingPackage(),
            new ImageResizerPackage(),
            new ImagePickerPackage(),
            new MapsPackage(),
            new SplashScreenPackage(),
            new LinearGradientPackage(),
            new GPSTrackerPackage()
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
  }
}
