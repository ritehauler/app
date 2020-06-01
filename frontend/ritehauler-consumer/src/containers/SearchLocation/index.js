// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, ScrollView, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";

import { SearchBar } from "../../appComponents";
import styles from "./styles";
import { Images, Metrics, Colors, Strings } from "../../theme";
import SectionListLocations from "./SectionListLocations";
import SectionListCities from "./SectionListCities";

const LOCATIONS = "locations";
const CITIES = "cities";

export default class SearchLocation extends Component {
  static propTypes = {
    location: PropTypes.object.isRequired,
    city: PropTypes.object.isRequired,
    updateNearByList: PropTypes.bool.isRequired,
    callback: PropTypes.func.isRequired,
    onCitySelected: PropTypes.func.isRequired
  };
  constructor(props) {
    super(props);
    this.onRightSearchPress = this.onRightSearchPress.bind(this);
    this.onSearchTextChange = this.onSearchTextChange.bind(this);
    this.onCitySelected = this.onCitySelected.bind(this);
    this.onLocationSelected = this.onLocationSelected.bind(this);
    this.getCityName = this.getCityName.bind(this);

    this.selectedCity = this.props.city;
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  onRightSearchPress() {
    this.selectedView = this.selectedView === LOCATIONS ? CITIES : LOCATIONS;
    if (this.selectedView === LOCATIONS) {
      this._scrollTo(0);
      this.setSearchBarForLocations();
      this.sectionListLocations.setSearchText("");
    } else {
      this._scrollTo();
      this.setSearchBarForCities();
      this.sectionListCities.setSearchText("");
    }
  }

  onSearchTextChange = searchText => {
    if (this.selectedView === LOCATIONS) {
      this.sectionListLocations.setSearchText(searchText, this.selectedCity);
    } else {
      this.sectionListCities.setSearchText(searchText);
    }
  };

  onCitySelected(city) {
    Keyboard.dismiss();
    if (!_.isEqual(this.selectedCity, city)) {
      this.selectedCity = city;
      this.sectionListLocations.fetchDataNearByLocations(city, city);
      this.props.onCitySelected(this.selectedCity);
    }
    this.onRightSearchPress();
  }

  onLocationSelected(data) {
    Keyboard.dismiss();
    this.props.callback(data);
    Actions.pop();
  }

  setSearchBarForLocations() {
    this.searchBar.setValues(
      "",
      this.getCityName(),
      Images.arrowDownLight,
      Colors.text.searchLabel,
      Strings.searchForLocation
    );
  }

  setSearchBarForCities() {
    this.searchBar.setValues(
      "",
      this.getCityName(),
      Images.arrowDownDark,
      Colors.text.primary,
      Strings.searchForCity
    );
  }

  getCityName() {
    return this.selectedCity.name || "Select City";
  }

  selectedView = LOCATIONS;
  selectedCity = {};

  _scrollTo(x = Metrics.screenWidth, animated = false) {
    this.scrollView.scrollTo({ x, animated });
  }

  _renderSearchBar() {
    return (
      <SearchBar
        hasRightView
        rightText={this.getCityName()}
        ref={ref => {
          this.searchBar = ref;
        }}
        rightImage={Images.arrowDownLight}
        placeHolder={Strings.searchForLocation}
        textSearch=""
        rightTextColor="searchLabel"
        onRightViewPress={this.onRightSearchPress}
        onSearchTextChange={this.onSearchTextChange}
      />
    );
  }

  _renderSectionListCities() {
    return (
      <SectionListCities
        ref={el => {
          if (!!el) {
            this.sectionListCities = el.getWrappedInstance();
          }
        }}
        onPressItem={this.onCitySelected}
      />
    );
  }

  _renderSectionListLocations() {
    const { location, updateNearByList } = this.props;
    return (
      <SectionListLocations
        ref={el => {
          if (!!el) {
            this.sectionListLocations = el.getWrappedInstance();
          }
        }}
        onPressItem={this.onLocationSelected}
        location={location}
        updateNearByList={updateNearByList}
        selectedCity={this.selectedCity}
      />
    );
  }

  _renderSectionLists() {
    return (
      <ScrollView
        horizontal
        pagingEnabled
        scrollEnabled={false}
        keyboardShouldPersistTaps="handled"
        ref={ref => {
          this.scrollView = ref;
        }}
        showsHorizontalScrollIndicator={false}
      >
        {this._renderSectionListLocations()}
        {this._renderSectionListCities()}
      </ScrollView>
    );
  }

  render() {
    return (
      <View style={styles.container}>
        {this._renderSearchBar()}
        {this._renderSectionLists()}
      </View>
    );
  }
}
