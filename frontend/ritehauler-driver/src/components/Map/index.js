// @flow
import React, { Component } from "react";
import MapView, { PROVIDER_GOOGLE } from "react-native-maps";
import PropTypes from "prop-types";
import _ from "lodash";
import styles from "./styles";
import { Images, Colors, Metrics } from "../../theme";
import mapStyle from "../../theme/MapStyle.json";
import Util from "../../util";
import { MapRoutePresenter } from "../../presenter";

class Map extends Component {
  static propTypes = {
    onMarkerPress: PropTypes.func,
    region: PropTypes.object,
    markers: PropTypes.array,
    mapRoute: PropTypes.object,
    children: PropTypes.node
  };

  static defaultProps = {
    onMarkerPress: () => {},
    markers: undefined,
    mapRoute: {},
    region: {},
    children: undefined
  };

  shouldComponentUpdate(nextProps) {
    return !_.isEqual(nextProps, this.props);
  }

  getMapViewRef = () => this.mapView;

  _renderRoute() {
    const { mapRoute } = this.props;

    if (mapRoute.data && !Util.isEmpty(mapRoute.data)) {
      const routeCoordinates = MapRoutePresenter.getRouteCoordinates(mapRoute);

      return (
        <MapView.Polyline
          coordinates={routeCoordinates}
          strokeColor={Colors.background.routeColor} // fallback for when `strokeColors` is not supported by the map-provider
          strokeWidth={Metrics.routeStrokeWidth}
        />
      );
    }

    return null;
  }

  _renderMarkers() {
    const { onMarkerPress, markers } = this.props;
    const {
      markerLocateMe,
      markerOrderSelected,
      markerOrder,
      markerWarehouse
    } = Images;

    let _markers = null;
    if (markers && markers.length > 0) {
      _markers = markers.map((marker, index) => (
        <MapView.Marker
          onPress={() => onMarkerPress(marker, index)}
          zIndex={index}
          key={marker.id}
          coordinate={marker.location}
          image={
            marker.isCurrentLocation
              ? markerLocateMe
              : marker.isWarehouseLocation
                ? markerWarehouse
                : marker.isactive
                  ? markerOrderSelected
                  : markerOrder
          }
        />
      ));
    }
    return _markers;
  }

  render() {
    const { region, children, markers, onMarkerPress, ...rest } = this.props;
    const _markers = this._renderMarkers();

    return (
      <MapView
        ref={ref => {
          this.mapView = ref;
        }}
        showsUserLocation={true}
        style={styles.mapStyle}
        initialRegion={region}
        customMapStyle={mapStyle}
        provider={PROVIDER_GOOGLE}
        {...rest}
      >
        {children}
        {_markers}
        {this._renderRoute()}
      </MapView>
    );
  }
}

export default Map;
