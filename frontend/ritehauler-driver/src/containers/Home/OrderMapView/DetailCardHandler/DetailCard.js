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
import { ImageView, BottomButton } from "../../../../components";
import { Metrics, Images, Colors, ApplicationStyles } from "../../../../theme";
import LinearGradient from "react-native-linear-gradient";
import { Actions } from "react-native-router-flux";
import Utils from "../../../../util";

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
          placeholderSource={Images.personPlaceholder}
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

  renderArrivalDetail(status) {
    let carImage = Images.car;
    let homeImage = Images.halfway_grey;
    let destinationImage = Images.reached_grey;

    let colorArray = Colors.lgGreyArray;

    if (status === 1) {
      carImage = Images.car;
      homeImage = Images.halfway_grey;
      destinationImage = Images.reached_grey;
    } else if (status === 2) {
      carImage = Images.car;
      homeImage = Images.halfway;
      destinationImage = Images.reached_grey;
      colorArray = Colors.lgHalfGreyArray;
    } else if (status === 3) {
      carImage = Images.car;
      homeImage = Images.halfway;
      destinationImage = Images.reached_grey;
      colorArray = Colors.lgColArray;
    } else {
      carImage = Images.car;
      homeImage = Images.halfway;
      destinationImage = Images.reached;
      colorArray = Colors.lgColArray;
    }

    return (
      <View style={styles.arrivalWrapper} overflow="hidden">
        <LinearGradient
          colors={colorArray}
          start={{ x: 0.0, y: 0 }}
          end={{ x: 0.6, y: 0 }}
          style={{
            flexDirection: "row",
            height: Metrics.ratio(3),
            width: Metrics.screenWidth - Metrics.doubleBaseMargin * 2,
            position: "absolute"
          }}
        />
        <View style={styles.iconWrapper}>
          <Image source={carImage} style={styles.icon} />
        </View>

        <View style={styles.iconWrapper}>
          <Image source={homeImage} style={styles.icon} />
        </View>

        <View style={styles.iconWrapper}>
          <Image source={destinationImage} style={styles.icon} />
        </View>
      </View>
    );
  }

  renderButton(title) {
    return (
      <BottomButton
        title={title}
        onPress={() => {
          this.props.cbOnButtonPress();
        }}
      />
    );
  }

  renderActivityIndicator() {
    return (
      <View style={styles.activityIndicatorWrapper}>
        <ActivityIndicator size="small" color="white" />
      </View>
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
      status,
      buttonTitle,
      isFetching,
      orderNumber
    } = this.props;

    return (
      <Animated.View style={[styles.mainContainer, translateAnimation]}>
        <TouchableWithoutFeedback
          onPress={() =>
            Actions.orderSummary({
              title: orderNumber ? orderNumber : "Order Summary"
            })
          }
        >
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
            {status && this.renderArrivalDetail(status)}
          </View>
        </TouchableWithoutFeedback>
        {status && this.renderButton(buttonTitle)}
        {isFetching && this.renderActivityIndicator()}
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
    alignItems: "center"
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
    width: Utils.isPlatformAndroid()
      ? Metrics.icon.normal
      : Metrics.icon.small * 1.5,
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
