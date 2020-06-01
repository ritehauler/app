// @flow
import _ from "lodash";
import React, { Component } from "react";
import { FlatList } from "react-native";
import PropTypes from "prop-types";

import { EmptyViewRequest } from "../../../components";
import { Strings } from "../../../theme";
import styles from "./styles";
import Item from "../Item";

export default class ListNames extends Component {
  static propTypes = {
    selectedId: PropTypes.number,
    onPressItem: PropTypes.func,
    data: PropTypes.array.isRequired
  };

  static defaultProps = {
    selectedId: 0,
    onPressItem: undefined
  };

  constructor(props) {
    super(props);
    this.renderItem = this.renderItem.bind(this);
    this.onPressItem = this.onPressItem.bind(this);
    this.renderEmptyView = this.renderEmptyView.bind(this);
  }

  state = {
    selectedId: this.props.selectedId,
    data: this.props.data,
    searchText: ""
  };

  shouldComponentUpdate(nextProps, nextState) {
    return !_.isEqual(nextState, this.state);
  }

  onPressItem(item) {
    if (this.state.selectedId !== item.id) {
      this.setState({ selectedId: item.entity_id });
    }
    if (this.props.onPressItem) {
      this.props.onPressItem(item);
    }
  }

  setSearchResults = searchText => {
    if (searchText !== this.state.searchText) {
      if (searchText === "") {
        this.setState({ data: this.props.data, searchText: "" });
      } else {
        this.setState({
          data: this.getSearchNames(searchText.toLowerCase()),
          searchText
        });
      }
    }
  };

  getSearchNames = searchText => {
    return _.filter(this.props.data, function(o) {
      const location = o.title.toLowerCase();
      return location.indexOf(searchText) !== -1;
    });
  };

  setSelectedId = selectedId => {
    if (this.state.selectedId !== selectedId) {
      this.setState({ selectedId });
    }
  };

  keyExtractor(item) {
    return `${item.entity_id}`;
  }

  renderItem({ item }) {
    const { selectedId } = this.state;
    return (
      <Item
        data={item}
        onPress={this.onPressItem}
        isSelected={item.entity_id === selectedId}
      />
    );
  }

  renderEmptyView() {
    return (
      <EmptyViewRequest
        message={Strings.noItemNamesSearch}
        style={styles.emptyView}
      />
    );
  }

  render() {
    const { data } = this.state;
    return (
      <FlatList
        style={styles.list}
        data={data}
        renderItem={this.renderItem}
        keyExtractor={this.keyExtractor}
        extraData={this.state.selectedId}
        ListEmptyComponent={this.renderEmptyView}
      />
    );
  }
}
