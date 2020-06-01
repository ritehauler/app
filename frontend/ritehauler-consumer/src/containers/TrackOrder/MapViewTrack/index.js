// @flow
import _ from "lodash";
import React from "react";
import { View } from "react-native";
import MapView, { PROVIDER_GOOGLE, Marker, Polyline } from "react-native-maps";
import PropTypes from "prop-types";

import styles from "./styles";
import { Images, Colors } from "../../../theme";

export default class MapViewTrack extends React.PureComponent {
  static propTypes = {
    pickUpLatLong: PropTypes.object,
    dropOffLatLong: PropTypes.object,
    coordinates: PropTypes.array
  };

  static defaultProps = {
    pickUpLatLong: undefined,
    dropOffLatLong: undefined,
    coordinates: []
  };

  constructor(props) {
    super(props);
    this.onMapReady = this.onMapReady.bind(this);
  }

  onMapReady() {
    // get data from props
    const { pickUpLatLong, dropOffLatLong, coordinates } = this.props;

    // merge pick up , dropoff and coordinates array
    const pickupAndDropOffArray = [];
    if (pickUpLatLong) {
      pickupAndDropOffArray.push(pickUpLatLong);
    }
    if (dropOffLatLong) {
      pickupAndDropOffArray.push(dropOffLatLong);
    }
    const fitCoordinates = _.concat(pickupAndDropOffArray, coordinates);

    // set fit to coordinates
    if (fitCoordinates.length > 0) {
      this.mapView.fitToCoordinates(fitCoordinates, {
        edgePadding: {
          top: 60,
          right: 40,
          bottom: 60,
          left: 40
        }
      });
    }
  }

  _renderMarker(coordinate, image, zIndex) {
    return <Marker coordinate={coordinate} image={image} zIndex={zIndex} />;
  }
  render() {
    const { pickUpLatLong, dropOffLatLong, coordinates } = this.props;

    return (
      <View style={styles.container}>
        <MapView
          ref={ref => {
            this.mapView = ref;
          }}
          provider={PROVIDER_GOOGLE}
          onMapReady={this.onMapReady}
          style={styles.map}
          initialRegion={{
            latitude: pickUpLatLong.latitude,
            longitude: pickUpLatLong.longitude,
            latitudeDelta: 0.008738120968118324,
            longitudeDelta: 0.005989372730255127
          }}
        >
          {pickUpLatLong &&
            this._renderMarker(pickUpLatLong, Images.pickUpMarker, 1)}
          {dropOffLatLong &&
            this._renderMarker(dropOffLatLong, Images.dropOffMarker, 2)}
          {coordinates.length > 0 && (
            <Polyline
              coordinates={coordinates}
              strokeColor={Colors.accent}
              fillColor={Colors.accent}
              strokeWidth={6}
            />
          )}
        </MapView>
      </View>
    );
  }
}
