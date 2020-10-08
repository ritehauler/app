import React, { PureComponent } from "react";
import { Image, Text, View, TouchableOpacity } from "react-native";
import LinearGradient from "react-native-linear-gradient";
import { Actions } from "react-native-router-flux";
import { Images, Colors, Metrics, ApplicationStyles } from "../../theme";
import Utils from "../../util";

export default class CustomNavBar extends PureComponent {
  render() {
    return (
      <LinearGradient
        colors={Colors.lgColArray}
        start={{ x: 0.0, y: 0 }}
        end={{ x: 0.8, y: 0 }}
        style={{
          height: Metrics.navBarHeight,
          width: Metrics.screenWidth
        }}
      >
        <View
          style={{
            flexDirection: "row",
            flex: 1,
            marginTop: Utils.isPlatformAndroid()
              ? 0
              : Utils.isPhoneX()
                ? 0
                : Metrics.baseMargin
          }}
        >
          <TouchableOpacity
            onPress={() => Actions.pop()}
            style={{
              paddingLeft: Metrics.smallMargin,
              paddingRight: Metrics.baseMargin
            }}
          >
            <Image
              source={
                Utils.isPlatformAndroid()
                  ? Images.backWhiteAndroid
                  : Images.backWhite
              }
              resizeMode={Utils.isPlatformAndroid() ? "cover" : "contain"}
              style={{
                width: Utils.isPlatformAndroid()
                  ? Metrics.icon.small * 1.6
                  : Metrics.icon.small * 1.2,
                height: Utils.isPlatformAndroid()
                  ? Metrics.icon.small * 1.6
                  : Metrics.icon.small * 1.2,
                marginTop: Utils.isPhoneX()
                  ? Metrics.doubleBaseMargin + Metrics.ratio(2)
                  : Utils.isPlatformAndroid()
                    ? Metrics.smallMargin + Metrics.ratio(5)
                    : Metrics.baseMargin + Metrics.ratio(2)
              }}
            />
          </TouchableOpacity>
          <Text
            style={[
              {
                textAlign: "center",
                flex: 1,
                marginTop: Utils.isPhoneX()
                  ? Metrics.doubleBaseMargin
                  : Metrics.smallMargin * 1.4,
                marginRight: Metrics.doubleBaseMargin + Metrics.baseMargin
              },
              ApplicationStyles.dBoldW20
            ]}
          >
            Profile
          </Text>
        </View>
      </LinearGradient>
    );
  }
}
