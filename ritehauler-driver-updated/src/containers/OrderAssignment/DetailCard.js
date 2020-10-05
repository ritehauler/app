import React, { PureComponent } from "react";
import {
  View,
  Animated,
  Text,
  Easing,
  Image,
  TouchableWithoutFeedback,
  ActivityIndicator
} from "react-native";
import { ImageView, BottomButton } from "../../components";
import { Metrics, Images, Colors, ApplicationStyles } from "../../theme";
import LinearGradient from "react-native-linear-gradient";
import { Actions } from "react-native-router-flux";
import Utils from "../../util";

export default class DetailCard extends PureComponent {
  constructor(props) {
    super(props);
    this.animationValue = new Animated.Value(Metrics.screenHeight);
    this.translateAnimation = new Animated.Value(0);
    this.opacityAnimation = new Animated.Value(0);
  }

  componentDidMount() {
    Animated.stagger(100, [
      Animated.timing(this.animationValue, {
        toValue: 0,
        duration: 600,
        easing: Easing.bezier(0.03, 0.94, 0.03, 0.95),
        useNativeDriver: true
      }),
      Animated.timing(this.translateAnimation, {
        toValue: 1,
        duration: 400,
        useNativeDriver: true
      }),
      Animated.timing(this.opacityAnimation, {
        toValue: 1,
        duration: 400,
        useNativeDriver: true
      })
    ]).start();
  }

  renderPersonDetail(image, name, distance) {
    const translateAnimation = {
      transform: [
        {
          translateY: this.translateAnimation.interpolate({
            inputRange: [0, 1],
            outputRange: [50, 0],
            extrapolate: "clamp"
          })
        }
      ]
    };
    const opacityValue = { opacity: this.opacityAnimation };
    return (
      <Animated.View
        style={(translateAnimation, opacityValue, styles.personalDetailWrapper)}
      >
        <ImageView
          isShowActivity={false}
          source={Utils.getValidImage(image)}
          style={[
            styles.circleImageStyle,
            {
              marginRight: Metrics.baseMargin
            }
          ]}
          customImagePlaceholderDefaultStyle={styles.circleImageStyle}
          borderRadius={Metrics.thumbImageRadius}
          placeholderSource={Images.imagePic1}
        />
        <Text style={[{ flex: 1 }, ApplicationStyles.tBold16]}>{name}</Text>
        {distance && (
          <Text style={ApplicationStyles.re16Black}>{`${distance} mi`}</Text>
        )}
      </Animated.View>
    );
  }

  renderLocationDetail(image, title, address, index) {
    const translateAnimation = {
      transform: [
        {
          translateY: this.translateAnimation.interpolate({
            inputRange: [0, 1],
            outputRange: [50, 0],
            extrapolate: "clamp"
          })
        }
      ]
    };
    const opacityValue = { opacity: this.opacityAnimation };
    return (
      <Animated.View
        style={[styles.itemDetailWrapper, translateAnimation, opacityValue]}
      >
        <View>
          <View
            style={[
              styles.join,
              { top: index > 1 ? 0 : Metrics.thumbImageWidth }
            ]}
          />
          <ImageView
            isShowActivity={false}
            source={Utils.getValidImage(image)}
            style={[
              styles.circleImageStyle,
              {
                marginRight: Metrics.baseMargin,
                marginVertical: Metrics.baseMargin
              }
            ]}
            customImagePlaceholderDefaultStyle={styles.circleImageStyle}
            borderRadius={Metrics.thumbImageRadius}
            placeholderSource={Images.imagePic1}
          />
        </View>
        <View>
          <Text style={[ApplicationStyles.re16Black, styles.locationText]}>
            {title}
          </Text>
          <Text style={[ApplicationStyles.re13Gray, styles.locationText]}>
            {address}
          </Text>
        </View>
      </Animated.View>
    );
  }
  render() {
    const translateAnimation = {
      transform: [
        {
          translateY: this.animationValue
        }
      ]
    };

    const {
      user,
      dropLocation,
      pickLocation,
      cbOnDetailCardPress
    } = this.props;

    return (
      <Animated.View style={[styles.mainContainer, translateAnimation]}>
        <TouchableWithoutFeedback onPress={() => cbOnDetailCardPress()}>
          <View style={styles.container}>
            {this.renderPersonDetail(user.image, user.name, user.distance)}
            {this.renderLocationDetail(
              Images.pickupLocation,
              "Pickup Location",
              pickLocation.address,
              1
            )}
            {this.renderLocationDetail(
              Images.dropLocation,
              "Dropoff Location",
              dropLocation.address,
              2
            )}
          </View>
        </TouchableWithoutFeedback>
      </Animated.View>
    );
  }
}

const styles = {
  mainContainer: {
    position: "absolute",
    bottom: Utils.isPhoneX() ? Metrics.doubleBaseMargin : -1,
    backgroundColor: Colors.transparent,
    width: Metrics.screenWidth,
    alignItems: "center",
    marginBottom: Metrics.smallMargin
  },
  container: {
    backgroundColor: Colors.white,
    padding: Metrics.baseMargin,
    width: Metrics.screenWidth - Metrics.baseMargin
  },
  circleImageStyle: {
    height: Metrics.thumbImageHeight,
    width: Metrics.thumbImageWidth,
    borderRadius: Metrics.thumbImageRadius,
    backgroundColor: "white"
  },
  join: {
    position: "absolute",
    width: 0.5,
    height: Metrics.thumbImageWidth + 10,
    left: Metrics.thumbImageWidth / 2,
    backgroundColor: Colors.text.quaternary
  },
  personalDetailWrapper: {
    flexDirection: "row",
    flex: 1,
    alignItems: "center"
  },
  itemDetailWrapper: {
    flexDirection: "row",
    alignItems: "center",
    flex: 1
  },
  icon: {
    width: Utils.isPlatformAndroid() ? Metrics.icon.normal : Metrics.icon.small,
    height: Utils.isPlatformAndroid()
      ? Metrics.icon.normal
      : Metrics.icon.small,
    resizeMode: "contain"
  },
  arrivalWrapper: {
    flexDirection: "row",
    alignItems: "center",
    marginTop: Metrics.smallMargin + Metrics.smallMargin / 2,
    marginBottom: Metrics.smallMargin,
    justifyContent: "space-between"
  },
  iconWrapper: {
    paddingHorizontal: Metrics.smallMargin / 2,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "white"
  },
  activityIndicatorWrapper: {
    position: "absolute",
    right: Metrics.baseMargin,
    bottom: Metrics.baseMargin * 1.1
  },
  locationText: {
    marginRight: Metrics.doubleBaseMargin
  }
};
