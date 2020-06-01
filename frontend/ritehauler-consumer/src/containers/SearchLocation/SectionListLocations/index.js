import _ from "lodash";
import { connect } from "react-redux";
import React from "react";
import { View, SectionList } from "react-native";
import PropTypes from "prop-types";

import { AppStyles, Strings, Metrics } from "../../../theme";
import Util from "../../../util";

import Item from "../Item";
import SectionHeader from "../SectionHeader";
import SectionFooter from "../SectionFooter";

import { GOOGLE_API_KEY, GOOGLE_RADIUS_CITY } from "../../../constants";
import {
  request as googleNearbyLocation,
  googlePlacesRequest,
  googlePlaceDetailRequest
} from "../../../actions/GoogleNearby";
import { Loader } from "../../../components";
import { EmptyView } from "../../../appComponents";

class SectionListLocations extends React.Component {
  static propTypes = {
    googleNearBy: PropTypes.object.isRequired,
    googleNearbyLocation: PropTypes.func.isRequired,
    onPressItem: PropTypes.func.isRequired,
    location: PropTypes.object.isRequired,
    googlePlaces: PropTypes.object.isRequired,
    googlePlacesRequest: PropTypes.func.isRequired,
    googlePlaceDetailRequest: PropTypes.func.isRequired,
    updateNearByList: PropTypes.bool.isRequired,
    stateCity: PropTypes.array.isRequired,
    selectedCity: PropTypes.object.isRequired,
    recentLocations: PropTypes.array.isRequired,
    user: PropTypes.object.isRequired
  };

  constructor(props) {
    super(props);
    this.renderItem = this.renderItem.bind(this);
    this.renderSectionFooter = this.renderSectionFooter.bind(this);
    this.viewAllRecentLocations = this.viewAllRecentLocations.bind(this);
    this.state = { searchText: "", showViewAllButton: true };
  }

  componentDidMount() {
    const { location, updateNearByList, selectedCity } = this.props;
    if (updateNearByList) {
      this.fetchDataNearByLocations(location, selectedCity);
    }
  }

  onItemPress = item => {
    const { onPressItem } = this.props;
    if (item.city && item.address) {
      onPressItem({
        name: item.address,
        location: {
          latitude: parseFloat(item.latitude),
          longitude: parseFloat(item.longitude)
        },
        city: item.city
      });
    } else {
      const payload = {
        placeid: item.place_id,
        key: GOOGLE_API_KEY
      };
      this.props.googlePlaceDetailRequest(payload, location => {
        let locality = "";
        let lat;
        let lng;
        if (location.geometry && location.geometry.location) {
          lat = location.geometry.location.lat;
          lng = location.geometry.location.lng;
        }
        location.address_components.forEach(component => {
          if (component.types.indexOf("locality") !== -1) {
            locality = component.long_name;
          }
        });
        if (locality !== "") {
          const selectedCity = this.getFilterCities(locality);
          if (selectedCity) {
            onPressItem({
              name: item.name || item.description,
              location: {
                latitude: lat,
                longitude: lng
              },
              city: selectedCity
            });
          } else {
            Util.alert(Strings.doesNotOperateMessage);
          }
        } else {
          Util.alert(Strings.doesNotOperateMessage);
        }
      });
    }
  };

  setSearchText = (searchText, city) => {
    if (this.state.searchText === searchText) {
      return;
    }
    if (searchText.length === 0) {
      this.setState({ searchText: "" });
    } else {
      this.state.searchText = searchText;
      this.fetchAutoSuggestPlaces(city, searchText);
    }
  };

  getFilterCities = locality => {
    const cities = this.props.stateCity;
    let selectedCity;
    const len = cities.length;
    const pattern = new RegExp(locality, "i");
    for (let i = 0; i < len; i += 1) {
      const { data } = cities[i];
      const citiesArray = _.filter(data, city => {
        return city.name.search(pattern) !== -1;
      });
      if (citiesArray.length > 0) {
        selectedCity = citiesArray[0];
        break;
      }
    }
    return selectedCity;
  };

  fetchDataNearByLocations = (location, city, isRetry = false) => {
    this.location = location;
    this.city = city;
    this.state.showViewAllButton = true;
    const { data, previousLocation } = this.props.googleNearBy;
    const latLng = `${location.latitude},${location.longitude}`;
    if (data.length === 0 || previousLocation !== latLng || isRetry) {
      const payload = {
        location: latLng,
        rankby: "distance",
        key: GOOGLE_API_KEY,
        city_id: city.entity_id || -1,
        customer_id: this.props.user.entity_id
      };
      this.props.googleNearbyLocation(payload);
    }
  };

  fetchAutoSuggestPlaces(city, searchText) {
    this.city = city;
    this.searchText = searchText;
    let payload;
    if (city && city.latitude) {
      const latLng = `${city.latitude},${city.longitude}`;
      payload = {
        location: latLng,
        input: `${searchText}`,
        key: GOOGLE_API_KEY,
        radius: GOOGLE_RADIUS_CITY,
        strictbounds: true
      };
    } else {
      payload = {
        input: `${searchText}`,
        key: GOOGLE_API_KEY
      };
    }
    this.props.googlePlacesRequest(payload);
  }

  viewAllRecentLocations() {
    this.setState({
      showViewAllButton: false
    });
  }

  city;
  searchText;
  location;

  _getData = () => {
    const { googleNearBy, googlePlaces } = this.props;
    const { searchText } = this.state;
    if (searchText && searchText !== "") {
      return [
        {
          id: 1,
          title: Strings.searchLocations,
          data: googlePlaces.data
        }
      ];
    }

    if (this.props.recentLocations.length > 0) {
      const showViewAllButton =
        this.state.showViewAllButton && this.props.recentLocations.length > 3;

      return [
        {
          id: 1,
          title: Strings.recentLocation,
          data: showViewAllButton
            ? this.props.recentLocations.slice(0, 3)
            : this.props.recentLocations,
          showViewAllButton
        },
        {
          id: 2,
          title: googleNearBy.data.length > 0 ? Strings.nearByLocations : "",
          data: googleNearBy.data
        }
      ];
    }

    return [
      {
        id: 2,
        title: googleNearBy.data.length > 0 ? Strings.nearByLocations : "",
        data: googleNearBy.data
      }
    ];
  };

  renderItem({ item }) {
    return (
      <Item
        title={item.name || item.description || item.address || ""}
        onPress={() => this.onItemPress(item)}
      />
    );
  }

  renderSectionHeader({ section: { title } }) {
    return <SectionHeader title={title} />;
  }

  renderSectionFooter({ section: { showViewAllButton } }) {
    if (showViewAllButton) {
      return <SectionFooter onPress={this.viewAllRecentLocations} />;
    }
    return null;
  }

  renderListSeparator() {
    return null;
  }

  _onRetryPress = type => {
    if (type === 0) {
      this.fetchDataNearByLocations(this.location, this.city, true);
    } else if (type === 1) {
      this.fetchAutoSuggestPlaces(this.city, this.searchText);
    }
  };

  render() {
    const { googleNearBy, googlePlaces } = this.props;
    const data = this._getData();

    if (googleNearBy.isFetching) {
      return (
        <View style={AppStyles.sectionList}>
          <Loader loading />
        </View>
      );
    }

    const errorInNearByRequest =
      googleNearBy.failure && this.state.searchText === "";
    const errorInGooglePlayServicesRequest =
      googlePlaces.failure && this.state.searchText !== "";

    if (errorInNearByRequest || errorInGooglePlayServicesRequest) {
      const message = errorInNearByRequest
        ? googleNearBy.errorMessage
        : googlePlaces.errorMessage;

      const type = errorInNearByRequest ? 0 : 1;
      return (
        <View style={{ width: Metrics.screenWidth }}>
          <EmptyView
            title={message}
            description={Strings.noInternetFullMessage}
            buttonText={Strings.retry}
            onPressButton={() => this._onRetryPress(type)}
          />
        </View>
      );
    }

    return (
      <SectionList
        renderItem={this.renderItem}
        stickySectionHeadersEnabled
        renderSectionHeader={this.renderSectionHeader}
        renderSectionFooter={this.renderSectionFooter}
        ItemSeparatorComponent={this.renderListSeparator}
        sections={data}
        keyExtractor={(item, index) => item + index}
        style={AppStyles.sectionList}
        contentContainerStyle={AppStyles.contentContainerStyle}
        extraData={this.state.showViewAllButton}
        keyboardShouldPersistTaps="handled"
      />
    );
  }
}

const actions = {
  googleNearbyLocation,
  googlePlacesRequest,
  googlePlaceDetailRequest
};
const mapStateToProps = store => ({
  stateCity: store.stateCity.data,
  googleNearBy: store.googleNearBy,
  googlePlaces: store.googlePlaces,
  user: store.user.data,
  recentLocations: store.recentLocations.data
});

export default connect(
  mapStateToProps,
  actions,
  null,
  { withRef: true }
)(SectionListLocations);
