echo "Updating React Native Real Path"
cp core/RNGRPPackage.java node_modules/react-native-get-real-path/android/src/main/java/com/rngrp/RNGRPPackage.java

echo "Updating Scrollable tab view DefaultTabbar class"
cp core/DefaultTabBar.js node_modules/react-native-scrollable-tab-view/DefaultTabBar.js
cp core/SceneComponent.js node_modules/react-native-scrollable-tab-view/SceneComponent.js

echo "Updating Stripe cursor color"
cp core/colors.xml node_modules/tipsi-stripe/android/src/main/res/values/colors.xml

echo "Updating Text Input ios"
cp core/RCTBaseTextInputShadowView.m node_modules/react-native/Libraries/Text/TextInput/RCTBaseTextInputShadowView.m

echo "Updating React Navigation"
cp core/DrawerNavigator.js node_modules/react-navigation/src/navigators/DrawerNavigator.js

echo "Updating Keyboard manager to remove warning"
cp core/ReactNativeKeyboardManager.m /data/Svn/RiteHauler/node_modules/react-native-keyboard-manager/ios/ReactNativeKeyboardManager.m

echo "Updating Google Maps"
cp core/AIRGoogleMap.m node_modules/react-native-maps/lib/ios/AirGoogleMaps/AIRGoogleMap.m

echo "node_modules updated"
