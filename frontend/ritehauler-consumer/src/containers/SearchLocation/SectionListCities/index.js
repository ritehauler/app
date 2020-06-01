// @flow
import _ from "lodash";
import { connect } from "react-redux";
import React from "react";
import { View, SectionList } from "react-native";
import PropTypes from "prop-types";

import { AppStyles } from "../../../theme";
import Item from "../Item";
import SectionHeader from "../SectionHeader";
import { Loader } from "../../../components";

class SectionListCities extends React.PureComponent {
  static propTypes = {
    stateCity: PropTypes.object.isRequired,
    onPressItem: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.renderItem = this.renderItem.bind(this);
    this.state = {
      data: props.stateCity.data
    };
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.stateCity.isFetching &&
      !nextProps.stateCity.isFetching &&
      !nextProps.stateCity.failure
    ) {
      this.setState({
        data: nextProps.stateCity.data
      });
    }
  }

  setSearchText = searchText => {
    if (this.searchText === searchText) {
      return;
    }
    if (searchText.length === 0) {
      this.setState({ data: this.props.stateCity.data });
    } else {
      const filterCities = this.getFilterCities(searchText.toLowerCase());
      this.setState({ data: filterCities });
      this.searchText = searchText;
    }
  };

  getFilterCities(searchText) {
    const cities = this.props.stateCity.data;
    const filterCities = [];
    const len = cities.length;
    const pattern = new RegExp(searchText, "i");
    for (let i = 0; i < len; i += 1) {
      const { data, ...rest } = cities[i];
      const citiesArray = _.filter(data, city => {
        return city.name.search(pattern) !== -1;
      });

      if (citiesArray.length > 0) {
        filterCities.push({ ...rest, data: citiesArray });
      }
    }
    return filterCities;
  }

  searchText = "";

  renderItem({ item }) {
    const { onPressItem } = this.props;
    return (
      <Item
        title={item.name}
        id={item.entity_id}
        onPress={() => onPressItem(item)}
      />
    );
  }

  renderSection({ section: { name } }) {
    return <SectionHeader title={name} />;
  }

  renderListSeparator() {
    return null;
  }

  render() {
    const { data } = this.state;
    const { isFetching } = this.props.stateCity;
    if (isFetching) {
      return (
        <View style={AppStyles.sectionList}>
          <Loader loading />
        </View>
      );
    }
    return (
      <SectionList
        renderItem={this.renderItem}
        stickySectionHeadersEnabled
        renderSectionHeader={this.renderSection}
        sections={data}
        keyExtractor={(item, index) => item + index}
        style={AppStyles.sectionList}
        contentContainerStyle={AppStyles.contentContainerStyle}
        ItemSeparatorComponent={this.renderListSeparator}
        keyboardShouldPersistTaps="handled"
      />
    );
  }
}

const actions = {};
const mapStateToProps = store => ({
  stateCity: store.stateCity
});

export default connect(
  mapStateToProps,
  actions,
  null,
  { withRef: true }
)(SectionListCities);
