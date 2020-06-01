// @flow
import React from "react";
import PropTypes from "prop-types";
import MapView, { PROVIDER_GOOGLE, Marker } from "react-native-maps";

import { INITIAL_REGION_MAP } from "../../../constants";
import { Metrics, Images } from "../../../theme";
import Util from "../../../util";
import styles from "./styles";

export default class MapViewLocation extends React.PureComponent {
  static propTypes = {
    pickUpLatLong: PropTypes.object,
    dropOffLatLong: PropTypes.object
  };

  static defaultProps = { pickUpLatLong: undefined, dropOffLatLong: undefined };

  fitToCoordinates() {
    const { pickUpLatLong, dropOffLatLong } = this.props;
    this.mapView.fitToCoordinates([pickUpLatLong, dropOffLatLong], {
      edgePadding: {
        top: Util.isPlatformIOS()
          ? Metrics.screenHeight / 3
          : Metrics.screenHeight,
        right: 40,
        bottom: Metrics.screenHeight / 5,
        left: 40
      },
      animated: true
    });
  }

  animateToRegion = region => {
    this.mapView.animateToRegion({
      latitude: region.latitude,
      longitude: region.longitude,
      latitudeDelta: 0.007276482634306802,
      longitudeDelta: 0.006304197013378143
    });
  };

  _renderMarker(coordinate, image, zIndex) {
    return <Marker coordinate={coordinate} image={image} zIndex={zIndex} />;
  }

  render() {
    const { pickUpLatLong, dropOffLatLong, ...rest } = this.props;
    const enable = !(pickUpLatLong && dropOffLatLong);
    const pointerEvents = enable ? {} : { pointerEvents: "none" };
    return (
      <MapView
        ref={ref => {
          this.mapView = ref;
        }}
        provider={PROVIDER_GOOGLE}
        scrollEnabled={enable}
        zoomEnabled={enable}
        pitchEnabled={false}
        style={styles.map}
        showsTraffic={false}
        initialRegion={INITIAL_REGION_MAP}
        maxZoomLevel={18}
        {...pointerEvents}
        {...rest}
      >
        {pickUpLatLong &&
          this._renderMarker(pickUpLatLong, Images.pickUpMarker, 1)}
        {dropOffLatLong &&
          this._renderMarker(dropOffLatLong, Images.dropOffMarker, 2)}
      </MapView>
    );
  }
}
