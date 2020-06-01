import React, { PureComponent } from "react";
import { Marker } from "react-native-maps";
import { View, Animated, Text, Easing } from "react-native";
import { ImageView } from "../../../components";
import { Metrics, Images, Colors, ApplicationStyles } from "../../../theme";
import Utils from "../../../util";

export default class DetailCard extends PureComponent {
  constructor(props) {
    super(props);
  }

  componentDidMount() {}

  renderPersonDetail(image, name, distance) {
    return (
      <View style={styles.personalDetailWrapper}>
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
          <Text style={ApplicationStyles.re16Black}>{`${distance}mi`}</Text>
        )}
      </View>
    );
  }

  renderItemDetail(image, title, address, index) {
    return (
      <View style={[styles.itemDetailWrapper]}>
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
          <Text style={ApplicationStyles.sB16Black}>{title}</Text>
          <Text style={[ApplicationStyles.re13Gray, styles.locationText]}>
            {address}
          </Text>
        </View>
      </View>
    );
  }

  render() {
    const { user, dropLocation, pickLocation } = this.props;

    return (
      <Animated.View style={[styles.container]}>
        {this.renderPersonDetail(user.image, user.name, user.distance)}
        {this.renderItemDetail(
          Images.pickupLocation,
          "Pickup Location",
          pickLocation.address,
          1
        )}
        {this.renderItemDetail(
          Images.dropLocation,
          "Dropoff Location",
          dropLocation.address,
          2
        )}
      </Animated.View>
    );
  }
}

const styles = {
  container: {
    backgroundColor: Colors.white,
    padding: Metrics.baseMargin
    // ,
    // width: Metrics.screenWidth - Metrics.doubleBaseMargin
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
  locationText: {
    marginRight: Metrics.doubleBaseMargin
  }
};
