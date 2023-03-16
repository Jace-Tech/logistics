var app = angular.module('AllForward', ['ngSanitize']).config(function ($controllerProvider, $provide, $compileProvider) {
    // Let's keep the older references.
    app._controller = app.controller;
    app._service = app.service;
    app._factory = app.factory;
    app._value = app.value;
    app._directive = app.directive;
    app.controller = function (name, constructor) {
        $controllerProvider.register(name, constructor);
        return this;
    };
    // Provider-based service.
    app.service = function (name, constructor) {
        $provide.service(name, constructor);
        return this;
    };
    // Provider-based factory.
    app.factory = function (name, factory) {
        $provide.factory(name, factory);
        return this;
    };
    // Provider-based value.
    app.value = function (name, value) {
        $provide.value(name, value);
        return this;
    };
    // Provider-based directive.
    app.directive = function (name, factory) {
        $compileProvider.directive(name, factory);
        return this;
    };
});

;

app.service('ScheduleService', function () {
    // scheduleParams: object

    // portFrom: from port UNLO Code
    // portTo: to port UNLO Code
    // dateFrom: Date in DD-MM-YYYY format
    // dateTo: Date in DD-MM-YYYY format

    this.getOceanSchedules = async function (scheduleParams) {
        const systemCarriers = Q.getLookup('SystemCarriersLookup').items;
        const sched = await $.get(Q.resolveUrl('~/Administration/Schedule/GetOceanSchedules'), scheduleParams);

        const schedules = [];

        sched.routeGroupsList.forEach(group => {
            group.route.forEach(rout => {
                const carrier = systemCarriers.find(carrier => carrier.ScacCode === group.carrier.scac);

                const sc = {
                    carrierId: carrier && carrier.Id,
                    carrier: carrier && carrier.Name,
                    carrierScac: group.carrier.scac,
                    carrierImage: carrier && carrier.Image,
                    origin: group.por.location.unlocode,
                    destination: group.fnd.location.unlocode,
                    transitTime: rout.transitTime,
                    cutOffTime: rout.leg[0] && rout.leg[0].fromPoint.defaultCutoff,
                    departuretime: rout.por.etd,
                    arrivaltime: rout.fnd.eta,
                    originalSC: rout
                };

                rout.leg = rout.leg.filter(x => x.transportMode !== '---');

                sc.transshipments = [];

                if (rout.leg.length > 1) {
                    sc.transshipments = rout.leg.map(leg => ({
                        carrier: group.carrier.name,
                        carrierScac: group.carrier.scac,
                        origin: leg.fromPoint.location.unlocode,
                        transitTime: leg.transitTime,
                        departuretime: leg.fromPoint.etd,
                        arrivaltime: leg.toPoint.eta,
                        voyage: leg.externalVoyageNumber
                    }));
                }

                sc.transhipmentsCount = sc.transshipments.length ? sc.transshipments.length - 1 : 0;

                schedules.push(sc);
            });
        });

        return schedules;
    };

    this.getQuotesSchedules = async quote => {
        if (quote.schedules || quote.loadingSchedules) return;

        quote.loadingSchedules = true;
        quote.loadingSchedulesError = false;

        const portFrom = Q.getLookup('PortsLookup').items.find(
            p => p.FreightoolsPhysicalAddressId == quote.MasterPhysicalAddressFromId
        ).PortCode;
        const portTo = Q.getLookup('PortsLookup').items.find(
            p => p.FreightoolsPhysicalAddressId == quote.MasterPhysicalAddressToId
        ).PortCode;

        try {
            quote.schedules = await this.getOceanSchedules({
                portFrom,
                portTo,
                dateFrom: moment().format(),
                dateTo: moment(quote.Expiration).format(),
                SystemCarrierId: quote.SystemCarrierId
            });

            quote.loadingSchedules = false;
            quote.loadingSchedulesError = false;
        } catch (err) {
            quote.loadingSchedules = false;
            quote.loadingSchedulesError = true;
            Q.notifyError(err.statusText || err.message);
        }
    };
});

app.service('CommonServices', function () {
    this.downloadFile = function (link, name) {
        var ele = document.createElement('a');
        ele.setAttribute('download', name);
        ele.href = link;
        ele.target = '_blank';
        document.body.appendChild(ele);
        ele.click();
        document.body.removeChild(ele);
    };

    this.unique = function (arr, keyProps) {
        const kvArray = arr.map(entry => {
            const key = keyProps.map(k => entry[k]).join('|');
            return [key, entry];
        });
        const map = new Map(kvArray);
        return Array.from(map.values());
    };

    // measurementType: 0 = CM, 1 = INCH
    this.calculateCBM = function (width, length, height, measurementType) {
        if (!width || !length || !height) return 0;

        if (measurementType == 1) {
            width = width / 0.3937;
            length = length / 0.3937;
            height = height / 0.3937;
        }

        return (Number(width) * Number(length) * Number(height)) / 1000000;
    };

    this.isValidEmail = email =>
        String(email)
            .toLowerCase()
            .match(
                /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );

    this.isValidUrl = str => {
        let url;

        try {
            url = new URL(str);
        } catch (e) {
            return false;
        }

        return url.protocol === 'http:' || url.protocol === 'https:';
    };

    this.updateOnboardingStep = checkType => {
        angular.element(document.getElementById('app')).scope().completeOnboardStep(checkType);
    };

    this.getGoodWeight = good => {
        if (!good.UnitWeight) return 0;

        let weightInKG = Number(good.UnitWeight);

        if (good.MeasurmentUnit == 1) {
            weightInKG = weightInKG / 2.2046;
        }

        return Math.round(weightInKG * good.Quantity || 0);
    };

    this.getTotalWeightInKG = sp => {
        if (!sp || !sp.SalesProposalGoods) return 0;
        return sp.SalesProposalGoods.reduce((sum, good) => sum + this.getGoodWeight(good), 0);
    };

    this.getTotalVolume = sp => {
        if (!sp || !sp.SalesProposalGoods) return 0;

        return sp.SalesProposalGoods.reduce((sum, good) => {
            const { DimensionsW, DimensionsL, DimensionsH, MeasurmentUnit } = good;
            const cmb = this.calculateCBM(DimensionsW, DimensionsL, DimensionsH, MeasurmentUnit);
            return sum + cmb * (good.Quantity || 0);
        }, 0);
    };

    this.getMinTemp = goods => {
        if (!goods) return null;

        const degrees = goods.filter(g => typeof g.Degrees == 'number').map(g => g.Degrees);

        if (degrees.length) {
            return Math.min(...degrees);
        }

        return null;
    };

    this.getTotalSpUnits = goods => {
        if (!goods) return;

        return goods.reduce((sum, good) => sum + good.Quantity, 0);
    };

    this.getGoodsInfo = sp => {
        if (!sp || !sp.SalesProposalGoods) return null;

        const unitTypes = Q.getLookup('UnitTypesLookup').items;

        const goodsInfo = sp.SalesProposalGoods.map(good => {
            const goodName = unitTypes.find(ut => ut.FreightToolsUnitTypeId == good.FreightUnitTypeId).Name.replace(' Container', '');
            return `${goodName} X ${good.Quantity}`;
        }).join(', ');

        return goodsInfo;
    };

    this.getFullGoodsInfo = sp => {
        const unitTypes = Q.getLookup('UnitTypesLookup').items;
        const isFCL = sp.ServiceMixId == 2;

        const goodsInfo = sp.SalesProposalGoods.map(good => {
            const { FreightUnitTypeId, Quantity, DimensionsW, DimensionsL, DimensionsH, MeasurmentUnit } = good;

            const goodName = unitTypes.find(ut => ut.FreightToolsUnitTypeId == FreightUnitTypeId).Name.replace(' Container', '');

            if (isFCL) {
                return `${goodName} X ${Quantity}`;
            } else {
                const cbm = this.calculateCBM(DimensionsW, DimensionsL, DimensionsH, MeasurmentUnit);
                const weight = this.getGoodWeight(good);
                good.TotalWeight = weight * Quantity;
                good.UnitVolume = cbm * Quantity;
                good.TotalVolume = cbm * Quantity;
                return `${goodName} X ${Quantity} - LxWxH: ${DimensionsL || 0} X ${DimensionsW || 0} X ${
                    DimensionsH || 0
                } - KG: ${weight} - CBM: ${(cbm * Quantity).toFixed(2)}`;
            }
        }).join(', ');

        const totalInfo = `Total - KG: ${this.getTotalWeightInKG(sp).toFixed(0)} - CBM: ${this.getTotalVolume(sp).toFixed(2)}`;

        return isFCL ? goodsInfo : `${goodsInfo} ${totalInfo}`;
    };

    this.hasSimilarPorts = portId => Q.getLookup('PortsLookup').itemById[portId].SimilarPortIds;
});

app.service('BookingService', [
    'CommonServices',
    function (CommonServices) {
        this.openBookingModal = async (quote, $scope, isImporterQuote) => {
            $scope.selectedBookingModalTab = 'booking';
            $scope.selectedQuote = quote;
            $scope.selectedQuote.shippers = [{}];
            $scope.selectedQuote.consignees = [{}];
            $scope.selectedQuote.Commodities = [];
            $scope.selectedQuote.includeOriginCharges = false;
            $scope.selectedQuote.includeDestinationCharges = false;

            const isPro = AllForward.Authorization.userDefinition.CompanyIsPro;
            const discount = isPro && quote.ServiceMixId == 2 ? 50 : 0;

            if (!isImporterQuote) {
                $scope.selectedQuote.totalSellingUSD = $scope.selectedQuote.BaseTotalSellingUSD - discount;
            }

            $('#contactIds').val(AllForward.Authorization.userDefinition.UserId).trigger('change');
            $scope.selectedQuote.ShipmentContacts = AllForward.Authorization.userDefinition.UserId;

            $('#booking-modal').modal();

            this.getBooking().then(booking => {
                $scope.booking = booking;
            });
        };

        this.getBooking = () => $.get(Q.resolveUrl('~/Administration/Bookings/GetBooking'));

        this.getOriginActivitiesPrice = (activities, isExternal) => {
            if (!activities) return;

            if (isExternal) {
                return activities.filter(a => a.RouteOrder < 4).reduce((sum, act) => sum + act.TotalSellingUSD, 0);
            }

            return (
                activities
                    .filter(activity => activity.STLogicalActivity.STLogicalAddressId == 5)
                    // .filter(activity => activity.STLogicalActivity.STLogicalAddressId == 5 || activity.STLogicalActivity.STLogicalFreightId == 4)
                    .reduce((sum, act) => sum + act.TotalSellingUSD, 0)
            );
        };

        this.getDestinationActivitiesPrice = (activities, isExternal) => {
            if (!activities) return;

            if (isExternal) {
                return activities.filter(a => a.RouteOrder > 4).reduce((sum, act) => sum + act.TotalSellingUSD, 0);
            }
            return (
                activities
                    .filter(activity => activity.STLogicalActivity.STLogicalAddressId == 7)
                    // .filter(activity => activity.STLogicalActivity.STLogicalAddressId == 7 || activity.STLogicalActivity.STLogicalFreightId == 6)
                    .reduce((sum, act) => sum + act.TotalSellingUSD, 0)
            );
        };

        this.onToggleOriginCharges = quote => {
            const price = this.getOriginActivitiesPrice(quote.SalesProposalActivities, quote.IsExternalQuote);

            if (quote.includeOriginCharges) {
                quote.totalSellingUSD += price;
            } else {
                quote.totalSellingUSD -= price;
            }
        };

        this.onToggleDestinationCharges = quote => {
            const price = this.getDestinationActivitiesPrice(quote.SalesProposalActivities, quote.IsExternalQuote);

            if (quote.includeDestinationCharges) {
                quote.totalSellingUSD += price;
            } else {
                quote.totalSellingUSD -= price;
            }
        };

        this.submitBookingForm = async (booking, selectedSp, logId) => {
            const sp = Q.deepClone(selectedSp);

            if (Q.isEmptyOrNull(sp.ShipmentContacts)) {
                Q.notifyError('Missing Shipment Contacts');
                return;
            }

            if (!sp.shippers.some(s => s.Id) || !sp.consignees.some(s => s.Id)) {
                Q.notifyError('Missing Shipper or Consignee');
                return;
            }

            if (sp.StartFrom == 2) {
                booking.MasterFromUnlCode = Q.getLookup('PortsLookup').items.find(
                    p => p.FreightoolsPhysicalAddressId == sp.MasterPhysicalAddressFromId
                ).PortCode;
            } else {
                booking.Origin = sp.FromDetails;
            }
            if (sp.ArriveTo == 0) {
                booking.MasterToUnlCode = Q.getLookup('PortsLookup').items.find(
                    p => p.FreightoolsPhysicalAddressId == sp.MasterPhysicalAddressToId
                ).PortCode;
            } else {
                booking.Destination = sp.ToDetails;
            }

            booking.Commodities = sp.Commodities.join(',');
            booking.Reference = sp.Reference;
            booking.ShipmentContacts = sp.ShipmentContacts;
            booking.SpotRateId = sp.SpotRateId;
            booking.ReadyDate = sp.ReadyToLoad;
            booking.OfferId = sp.OfferId;
            booking.GoodsSummary = CommonServices.getGoodsInfo(sp);
            booking.FullGoodsInfo = CommonServices.getFullGoodsInfo(sp);
            booking.ShipperIds = sp.shippers
                .filter(s => s.Id)
                .map(s => s.Id)
                .join(',');
            booking.ConsigneeIds = sp.consignees
                .filter(s => s.Id)
                .map(s => s.Id)
                .join(',');
            booking.SystemCarrierId = sp.SystemCarrierId;
            booking.ShipmentType =
                sp.ServiceMixId == 1 ? AllForward.SystemEnums.ShipmentType.Air : AllForward.SystemEnums.ShipmentType.Ocean;
            booking.TotalSellingUsd = sp.totalSellingUSD;

            if (sp.IsExternalQuote) {
                sp.SalesProposalActivities = sp.SalesProposalActivities.filter(
                    a =>
                        a.RouteOrder == 4 ||
                        (a.RouteOrder < 4 && sp.includeOriginCharges) ||
                        (a.RouteOrder > 4 && sp.includeDestinationCharges)
                );
            } else {
                sp.SalesProposalActivities = sp.SalesProposalActivities.filter(
                    a =>
                        (a.STLogicalActivity.STLogicalAddressId != 5 &&
                            a.STLogicalActivity.STLogicalAddressId != 7 &&
                            a.ActivityId != 31915 &&
                            a.ActivityId != 31932) ||
                        (a.STLogicalActivity.STLogicalAddressId == 5 && sp.includeOriginCharges) ||
                        (a.STLogicalActivity.STLogicalAddressId == 7 && sp.includeDestinationCharges)
                );
            }

            sp.TotalSellingUSD = booking.TotalSellingUsd;
            sp.GoodsDetails = sp.GoodsDetails?.replace(/Container/g, '').split(',')[0] || booking.GoodsSummary;
            sp.CustomerFromTo = 1;

            if (sp.ServiceMixId == 1) booking.ServiceMix = AllForward.SystemEnums.ServiceMix.AIR;
            if (sp.ServiceMixId == 2) booking.ServiceMix = AllForward.SystemEnums.ServiceMix.FCL;
            if (sp.ServiceMixId == 3) booking.ServiceMix = AllForward.SystemEnums.ServiceMix.LCL;

            booking.OptionalSpJson = JSON.stringify(sp);

            const url = '~/Administration/Bookings/SetBooking';
            await $.post(Q.resolveUrl(url), { booking, quoteLogId: logId });

            $('#booking-modal .booking_back_btn').click();
            $('#booking-received-modal').modal();
        };
    }
]);

app.service('UserService', function () {
    this.createUser = (user, fileInput) => {
        Q.blockUI();

        const data = new FormData();

        if (fileInput.files && fileInput.files.length) {
            data.append('Files', fileInput.files[0]);
        }

        data.append('entity', JSON.stringify(user));

        return $.post({
            url: Q.resolveUrl('~/Administration/User/CreateUser'),
            contentType: false, // Not to set any content header
            processData: false, // Not to process data
            data
        })
            .then(member => {
                Q.reloadLookup('UserLookup');
                Q.blockUndo();
                Q.notifySuccess(`${member.DisplayName} was added successfully`);
                $('#team-member-modal .close').click();
                return member;
            })
            .catch(err => {
                Q.blockUndo();
                Q.notifyError(err.statusText);
                throw err;
            });
    };

    this.editUser = (user, previousRoleId, fileInput) => {
        const data = new FormData();

        if (fileInput.files && fileInput.files.length) {
            data.append('Files', fileInput.files[0]);
        }

        data.append('entity', JSON.stringify(user));
        data.append('previousRoleId', previousRoleId);

        Q.blockUI();

        return $.post({
            url: Q.resolveUrl('~/Administration/User/EditUser'),
            contentType: false, // Not to set any content header
            processData: false, // Not to process data
            data
        })
            .then(editedUser => {
                Q.reloadLookup('UserLookup');
                Q.blockUndo();
                Q.notifySuccess('Changes have been saved!');
                $('#team-member-modal .close').click();
                return editedUser;
            })
            .catch(err => {
                Q.blockUndo();
                Q.notifyError(err.statusText);
                throw err;
            });
    };

    this.deleteUser = async user => {
        Q.blockUI();

        try {
            await $.post(Q.resolveUrl('~/Administration/User/RemoveUser'), { userId: user.UserId });
            Q.reloadLookup('UserLookup');

            Q.blockUndo();
            Q.notifySuccess(`${user.DisplayName} was deleted successfully.`);
        } catch (err) {
            Q.blockUndo();
            Q.notifyError(err.statusText);
            throw err;
        }
    };

    this.updateUserNotifyStatus = user => {
        const ctrl = angular.element(document.getElementById('notification')).scope();
        ctrl.userNotify = user.InAppNotify;
        ctrl.$apply();
    };

    this.sendVerificationEmail = async () => {
        Q.blockUI();

        try {
            await $.post(Q.resolveUrl('~/Account/ResendVerificationEmail'));
            Q.blockUndo();
            Q.notifySuccess('Verification email has been sent');
        } catch (err) {
            Q.blockUndo();
            Q.notifyError(err.statusText);
        }
    };
});

app.service('ShipmentPartnersService', function () {
    this.getShipmentPartner = () => {
        const url = '~/Administration/ShipmentPartners/GetShipmentPartner';
        return $.get(Q.resolveUrl(url));
    };

    this.setShipmentPartner = partner => {
        const url = '~/Administration/ShipmentPartners/SetShipmentPartner';
        return $.post(Q.resolveUrl(url), { shipment: partner });
    };
});

app.service('FreightoolsService', function () {
    this.ports = Q.getLookup('PortsLookup');

    this.setSpMasterFromAddress = (sp, portFromId) => {
        if (Q.isEmptyOrNull(portFromId)) {
            return;
        }

        const index = sp.SalesProposalLogicalRoutings.findIndex(r => r.Service == 1 && r.Active);

        for (let i = 0; i < index; i++) {
            sp.SalesProposalLogicalRoutings[i].Active = false;
        }

        const port = this.ports.itemById[portFromId];
        const route = sp.SalesProposalLogicalRoutings[index];

        if (!port) {
            Q.notifyError('Port not found');
            return;
        }

        if (!port.FreightoolsPhysicalAddressId) {
            Q.notifyError('FreightoolsPhysicalAddressId not found');
            return;
        }

        sp.CountryFromId = port.CountryId;
        sp.MasterPhysicalAddressFromId = port.FreightoolsPhysicalAddressId;
        sp.MasterPhysicalAddressFromName = port.PortName;
        route.SalesProposalLogicalPhysicalAddresses[0].PhysicalAddressId = port.FreightoolsPhysicalAddressId;
        route.SalesProposalLogicalPhysicalAddresses[0].CountryId = port.CountryId;
        route.Active = true;

        this.setRoutingCountries(sp, false, port.CountryId);
    };

    this.setSpMasterToAddress = (sp, portToId) => {
        if (Q.isEmptyOrNull(portToId)) {
            return;
        }

        var index = sp.SalesProposalLogicalRoutings.findIndex(r => r.Service == 1 && r.Active && r.DefaultCountry == 1);

        for (let i = index + 1; i < sp.SalesProposalLogicalRoutings.length; i++) {
            // destination not active
            sp.SalesProposalLogicalRoutings[i].Active = false;
        }

        const port = this.ports.itemById[portToId];
        const route = sp.SalesProposalLogicalRoutings[index];

        if (!port) {
            Q.notifyError('Port not found');
            return;
        }

        if (!port.FreightoolsPhysicalAddressId) {
            Q.notifyError('FreightoolsPhysicalAddressId not found');
            return;
        }

        sp.CountryToId = port.CountryId;
        sp.MasterPhysicalAddressToId = port.FreightoolsPhysicalAddressId;
        sp.MasterPhysicalAddressToName = port.PortName;
        route.SalesProposalLogicalPhysicalAddresses[0].PhysicalAddressId = port.FreightoolsPhysicalAddressId;
        route.SalesProposalLogicalPhysicalAddresses[0].CountryId = port.CountryId;
        route.Active = true;

        this.setRoutingCountries(sp, true, port.CountryId);
    };

    this.getPhysicalAddress = async (unloCode, addressTypeId) => {
        const url = `~/Common/GetFreighToolsData?method=GetPhysicalAddress&parameters=UNLOCode=${unloCode}%26addressTypeId=${addressTypeId}`;
        const data = await $.get(Q.resolveUrl(url));
        return JSON.parse(data);
    };

    this.getPhysicalAddressById = async uid => {
        const url = `~/Common/GetFreighToolsData?method=GetPhysicalAddress&parameters=uid=${uid}`;
        const data = await $.get(Q.resolveUrl(url));
        return JSON.parse(data);
    };

    this.setRoutingCountries = (sp, routePosition, countryId) => {
        sp.SalesProposalLogicalRoutings.forEach(route => {
            if (route.SalesProposalLogicalPhysicalAddresses?.length) {
                if (route.PositionTo == routePosition) {
                    route.SalesProposalLogicalPhysicalAddresses[0].CountryId = countryId;
                }
            }
        });
    };

    this.getSalesProposals = async (sp, origin, logId) => {
        const data = await $.post(Q.resolveUrl('~/Administration/NewShipments/SearchPublicQuotations'), {
            sp: JSON.stringify(sp),
            origin,
            logId
        });

        const { Sps, LogId } = data;

        return {
            LogId,
            Sps
        };
    };

    this.getSpsBySimilarPorts = async (sp, portFromId, portToId, logId, origin) => {
        if (sp.StartFrom == 2 && sp.ArriveTo == 0) {
            const portFrom = Q.getLookup('PortsLookup').itemById[portFromId];
            const portTo = Q.getLookup('PortsLookup').itemById[portToId];

            const portsFrom = portFrom.SimilarPortIds
                ? [portFromId, ...portFrom.SimilarPortIds.split(',')]
                : [portFromId];
            const portsTo = portTo.SimilarPortIds ? [portToId, ...portTo.SimilarPortIds.split(',')] : [portToId];

            let result = null;

            for (const similarPortFromId of portsFrom) {
                this.setSpMasterFromAddress(sp, similarPortFromId);

                for (const similarPortToId of portsTo) {
                    await this.setSpMasterToAddress(sp, similarPortToId);

                    result = await this.getSalesProposals(sp, origin, logId);
                    if (result.Sps.length) return result;
                }
            }

            return result;
        } else if (sp.StartFrom == 2) {
            const portFrom = Q.getLookup('PortsLookup').itemById[portFromId];

            const portsFrom = portFrom.SimilarPortIds
                ? [portFromId, ...portFrom.SimilarPortIds.split(',')]
                : [portFromId];

            let result = null;

            for (const similarPortFromId of portsFrom) {
                this.setSpMasterFromAddress(sp, similarPortFromId);

                result = await this.getSalesProposals(sp, origin, logId);
                logId = result.LogId;

                if (result.Sps.length) return result;
            }

            return result;
        } else if (sp.ArriveTo == 0) {
            const portTo = Q.getLookup('PortsLookup').itemById[portToId];
            const portsTo = portTo.SimilarPortIds ? [portToId, ...portTo.SimilarPortIds.split(',')] : [portToId];

            let result = null;

            for (var similarPortToId of portsTo) {
                this.setSpMasterToAddress(sp, similarPortToId);

                result = await this.getSalesProposals(sp, origin, logId);

                if (result.Sps.length) return result;
            }

            return result;
        }
    };

    this.getActivities = (activities, service, isExternal) => {
        if (isExternal) {
            return activities.filter(activity => activity.Service == service);
        }

        if (service == 1) {
            return activities.filter(
                activity =>
                    activity.STLogicalActivity.STLogicalAddressId != 5 &&
                    activity.STLogicalActivity.STLogicalAddressId != 7 &&
                    // activity.STLogicalActivity.STLogicalFreightId != 4 &&
                    // activity.STLogicalActivity.STLogicalFreightId != 6 &&
                    activity.ActivityId != 31915 &&
                    activity.ActivityId != 31932
            );
        }

        if (service == 3) {
            return activities.filter(
                activity => activity.STLogicalActivity.STLogicalAddressId == 5 || activity.STLogicalActivity.STLogicalFreightId == 4
            );
        }

        if (service == 4) {
            return activities.filter(
                activity => activity.STLogicalActivity.STLogicalAddressId == 7 || activity.STLogicalActivity.STLogicalFreightId == 6
            );
        }
    };

    this.completeSpsData = sps => {
        const minPrice = Math.min(...sps.map(sp => sp.BaseTotalSellingUSD));
        const sp = sps.find(sp => sp.BaseTotalSellingUSD == minPrice);

        if (sp) sp.bestPrice = true;
    };
});


;

app.directive('sendmessage', function () {
    return function (scope, element, attrs) {
        var shiftDown = false;
        element.bind('keydown', function (event) {
            if (event.which === 17) {
                shiftDown = true;
            }

            if (event.which === 13 && shiftDown) {
                event.preventDefault();
                scope.sendMessage(scope.messageForm.$valid);
                scope.$apply();
            }
        });
        element.bind('keyup', function (event) {
            if (event.which === 17) {
                shiftDown = false;
            }
        });
    };
});

app.directive('datepicker', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, elem, attrs, ngModelCtrl) {
            var updateModel = function (dateText) {
                scope.$apply(function () {
                    ngModelCtrl.$setViewValue(dateText);
                });
            };
            var options = {
                format: attrs.dateFormat || 'dd/mm/yy',
                startDate: new Date(),
                showButtonPanel: false,
                orientation: 'bottom',
                autoclose: true,
                onSelect: function (dateText) {
                    updateModel(dateText);
                }
            };
            elem.datepicker(options);
        }
    };
});

/**
 * Datepicker ui with start date as parameter
 * param: min-date {ng-model}   Disable all the dates before that day
 */
app.directive('datepickerRange', function () {
    return {
        restrict: 'A',
        require: 'ngModel',
        link: function (scope, elem, attrs, ngModelCtrl) {
            scope.$watch(attrs.minDate, function (minDateValue) {
                $(elem).datepicker('destroy');
                if (minDateValue === '') {
                    minDateValue = '0';
                }
                $(elem).datepicker({
                    startDate: minDateValue,
                    onSelect: function (dateText) {
                        scope.$apply(function () {
                            ngModelCtrl.$setViewValue(dateText);
                        });
                    }
                });
            });
        }
    };
});

app.directive('googlecities', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attrs, ngModelCtrl) {
            var gPlace = new google.maps.places.Autocomplete(element[0], { types: ['(cities)'] });

            google.maps.event.addListener(gPlace, 'place_changed', async function () {
                var place = gPlace.getPlace();
                const address = await convertGooglePlaceToPhysicalAddress(place);

                scope.$apply(function () {
                    if (place.length != 0) {
                        ngModelCtrl.$setViewValue(`${address.City}%${address.CountryId}`);
                        element.val(address.City);
                        if (attrs.from) {
                            if (scope.onCityFromChange) scope.onCityFromChange();
                        } else {
                            if (scope.onCityToChange) scope.onCityToChange();
                        }
                    }
                });
            });
        }
    };
});

app.directive('stopPropagation', function () {
    return {
        restrict: 'A',
        link: function (scope, element) {
            element.bind('click', function (e) {
                e.stopPropagation();
            });
        }
    };
});

app.directive('allowNumbersOnly', function () {
    return {
        require: 'ngModel',
        link(scope, element, attr, ngModelCtrl) {
            const fromUser = text => {
                if (!text) return;

                const allowedChars = attr.allowedChars || '';
                const regex = new RegExp(`[^0-9${allowedChars}]`, 'g');

                let transformedInput = text.replace(regex, '');

                if (!transformedInput.includes(allowedChars)) transformedInput = String(Number(transformedInput));

                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            };
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('phoneNumber', function () {
    return {
        require: 'ngModel',
        link(scope, element, attr, ngModelCtrl) {
            const fromUser = text => {
                if (!text) return;

                const transformedInput = text.replace(/[^0-9]/g, '');

                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            };
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('currencyInput', function ($filter, $browser) {
    return {
        require: 'ngModel',
        link: function (scope, element, { currencySymbol }, ngModelCtrl) {
            function listener() {
                const value = element.val().replace(/\D/g, '');
                if (!value) return;

                element.val(currencySymbol + $filter('number')(value, 0));
            }

            ngModelCtrl.$parsers.push(function (viewValue) {
                return viewValue.replace(/\D/g, '');
            });

            ngModelCtrl.$render = function () {
                if (!ngModelCtrl.$viewValue) return element.val('');

                element.val(currencySymbol + $filter('number')(ngModelCtrl.$viewValue, 0));
            };

            element.bind('change', listener);

            element.bind('keydown', function ({ key, shiftKey }) {
                if (shiftKey || (key >= 48 && key <= 57) || (key >= 96 && key <= 105)) return false;
                $browser.defer(listener);
            });
        }
    };
});

app.directive('englishInput', function () {
    return {
        require: 'ngModel',
        link: function (scope, element, attr, ngModelCtrl) {
            function fromUser(text) {
                var transformedInput = text.replace(/[^\x00-\x7F]/g, '');
                if (transformedInput !== text) {
                    ngModelCtrl.$setViewValue(transformedInput);
                    ngModelCtrl.$render();
                }
                return transformedInput;
            }
            ngModelCtrl.$parsers.push(fromUser);
        }
    };
});

app.directive('file', function () {
    return {
        require: 'ngModel',
        restrict: 'A',
        link: function ($scope, el, attrs, ngModel) {
            el.bind('change', function (event) {
                var files = event.target.files;
                var file = files[0];

                ngModel.$setViewValue(file.name);
                $scope.$apply();
            });
        }
    };
});

app.directive('autogrow', [
    '$window',
    function ($window) {
        return {
            link: function ($scope, $element, $attrs) {
                $scope.attrs = {
                    rows: 1,
                    maxLines: 999
                };

                for (var i in $scope.attrs) {
                    if ($attrs[i]) {
                        $scope.attrs[i] = parseInt($attrs[i]);
                    }
                }

                $scope.getOffset = function () {
                    var style = $window.getComputedStyle($element[0], null),
                        props = ['paddingTop', 'paddingBottom'],
                        offset = 0;

                    for (var i = 0; i < props.length; i++) {
                        offset += parseInt(style[props[i]]);
                    }
                    return offset;
                };

                $scope.autogrowFn = function () {
                    var newHeight = 0,
                        hasGrown = false;
                    if ($element[0].scrollHeight - $scope.offset > $scope.maxAllowedHeight) {
                        $element[0].style.overflowY = 'scroll';
                        newHeight = $scope.maxAllowedHeight;
                    } else {
                        $element[0].style.overflowY = 'hidden';
                        $element[0].style.height = 'auto';
                        newHeight = $element[0].scrollHeight - $scope.offset;
                        hasGrown = true;
                    }
                    $element[0].style.height = (newHeight < 0 ? $scope.offset : newHeight) + 'px';
                    return hasGrown;
                };

                $scope.offset = $scope.getOffset();
                $scope.lineHeight = $element[0].scrollHeight / $scope.attrs.rows - $scope.offset / $scope.attrs.rows;
                $scope.maxAllowedHeight = $scope.lineHeight * $scope.attrs.maxLines - $scope.offset;

                $scope.$watch($attrs.ngModel, $scope.autogrowFn);

                // Extract css properties to spy on
                var spyProps = $attrs.autogrow ? $attrs.autogrow.split(',') : [];
                angular.forEach(spyProps, function (property) {
                    // Set a watcher on each property
                    $scope.$watch(function () {
                        return $element.css(property);
                    }, styleChangedCallBack);
                });

                function styleChangedCallBack(newValue, oldValue) {
                    if (newValue !== oldValue) {
                        $scope.autogrowFn();
                    }
                }

                if ($element[0].value != '') {
                    $scope.autogrowFn();
                }
            }
        };
    }
]);

app.directive('lazyload', function lazyLoad() {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            if (!attrs.lazysrc) {
                Q.notifyError('lazySrc attribute is missing!');
            }

            const options = {
                rootMargin: attrs.loadMargin || '400px',
                threshold: 1.0
            };

            const observer = new IntersectionObserver(loadImg, options);
            observer.observe(angular.element(element)[0]);

            function loadImg(changes) {
                changes.forEach(change => {
                    if (change.intersectionRatio > 0) {
                        change.target.src = attrs.lazysrc;
                    }
                });
            }
        }
    };
});


;

app.filter('countryName', function () {
    return function (countryId) {
        if (!Q.isEmptyOrNull(countryId)) {
            return Q.getLookup('CountriesLookup').items.find(c => c.Id == countryId).Name;
        }
        return '';
    };
});

app.filter('countryImage', function () {
    return function (countryId) {
        if (!Q.isEmptyOrNull(countryId)) {
            return Q.getLookup('CountriesLookup').items.find(c => c.Id == countryId).Image;
        }
        return '';
    };
});

app.filter('countryIdByPortCode', function () {
    return function (portCode) {
        if (!Q.isEmptyOrNull(portCode)) {
            return Q.getLookup('PortsLookup').items.find(p => p.PortCode == portCode).CountryId;
        }
        return '';
    };
});

app.filter('countryIdByPortId', function () {
    return function (portId) {
        if (!Q.isEmptyOrNull(portId)) {
            return Q.getLookup('PortsLookup').items.find(p => p.Id == portId).CountryId;
        }
        return '';
    };
});

app.filter('currencySymbol', function () {
    return function (currencyId) {
        if (!Q.isEmptyOrNull(currencyId) && currencyId != 0) {
            return Q.getLookup('CurrenciesLookup').items.find(c => c.Id == currencyId).Symbol;
        }
        return '';
    };
});

app.filter('unitTypeName', function () {
    return function (unitTypeId) {
        if (!Q.isEmptyOrNull(unitTypeId)) {
            return Q.getLookup('UnitTypesLookup').items.find(u => u.Id == unitTypeId).Name;
        }
        return '';
    };
});

app.filter('portName', function () {
    return function (portId) {
        if (!Q.isEmptyOrNull(portId)) {
            return Q.getLookup('PortsLookup').items.find(c => c.Id == portId).PortName;
        }
        return '';
    };
});

app.filter('portCode', function () {
    return function (portId) {
        if (!Q.isEmptyOrNull(portId)) {
            return Q.getLookup('PortsLookup').items.find(c => c.Id == portId).PortCode;
        }
        return '';
    };
});

app.filter('portNameByCode', function () {
    return function (code) {
        if (!Q.isEmptyOrNull(code)) {
            return Q.getLookup('PortsLookup').items.find(c => c.PortCode == code).PortName;
        }
        return '';
    };
});

app.filter('carrierName', function () {
    return function (carrierId) {
        if (!Q.isEmptyOrNull(carrierId)) {
            return Q.getLookup('SystemCarriersLookup').itemById[carrierId].Name;
        }
        return '';
    };
});

app.filter('carrierLogo', function () {
    return function (carrierId) {
        if (!Q.isEmptyOrNull(carrierId)) {
            const image = Q.getLookup('SystemCarriersLookup').itemById[carrierId].Image;
            if (!image) return '';
            return Q.resolveUrl('~/upload/') + image
        }
        return '';
    };
});

app.filter('countryIdbyPortCode', function () {
    return function (code) {
        if (!Q.isEmptyOrNull(code)) {
            return Q.getLookup('PortsLookup').items.find(c => c.PortCode == code).CountryId;
        }
        return '';
    };
});

app.filter('timeSince', function () {
    return function (datetime) {
        return moment.utc(datetime, moment.ISO_8601).fromNow();
    };
});

app.filter('UTCToLocal', function () {
    return function (datetime, format = 'DD/MM/YY') {
        return moment.utc(datetime).local().format(format);
    };
});

app.filter('nameInitional', function () {
    return function (name) {
        if (!Q.isEmptyOrNull(name)) {
            name = name.split(' ');
            return name[0].charAt(0) + '.' + name[name.length - 1].charAt(0);
        }
        return '';
    };
});

app.filter('range', function () {
    return function (val, range, isRoundUp = false) {
        if (isRoundUp) range = Math.ceil(range);
        else range = parseInt(range);

        for (var i = 0; i < range; i++) val.push(i);
        return val;
    };
});

app.filter('dateWithoutTime', function () {
    return function (date, dateFormat = 'DD.MM.YYYY') {
        if (!Q.isEmptyOrNull(date)) {
            return moment(date).format(dateFormat);
        }
        return '';
    };
});

app.filter('userDisplayName', function () {
    return function (userId) {
        if (!Q.isEmptyOrNull(userId)) {
            return Q.getLookup('UserLookup').items.find(u => u.UserId == userId).DisplayName;
        }
        return '';
    };
});

app.filter('companyName', function () {
    return function (companyId) {
        if (!Q.isEmptyOrNull(companyId)) {
            return Q.getLookup('CompaniesLookup').itemById[companyId].Name;
        }
        return '';
    };
});

app.filter('lineBreaks', function () {
    return function (text) {
        if (!Q.isEmptyOrNull(text)) {
            return text.split('\n').join('<br/>');
        }
        return '';
    };
});

app.filter('roundUp', function () {
    return function (value) {
        if (!Q.isEmptyOrNull(value)) {
            return Math.ceil(value);
        }
        return 0;
    };
});

app.filter('notInArray', $filter => (list, arrayFilter) => {
    if (arrayFilter) {
        return $filter('filter')(list, listItem => !arrayFilter.includes(listItem));
    }

    return false;
});

app.filter('daysDiff', () => (date1, date2) => {
    const daysDiff = Math.abs(moment(date2).startOf('day').diff(moment(date1).startOf('day'), 'days'));

    return daysDiff;
});

app.filter('daysDiffFromUnix', () => (date1, date2) => {
    const daysDiff = Math.abs(moment.unix(date1).startOf('day').diff(moment.unix(date2).startOf('day'), 'days'));

    const daysPostfix = daysDiff === 1 ? 'Day' : 'Days';

    return `${daysDiff} ${daysPostfix}`;
});

app.filter('abs', () => num => Math.abs(num));

app.filter('roleName', function () {
    return function (roleId) {
        if (!Q.isEmptyOrNull(roleId)) {
            const role = Q.getLookup('RoleLookup').items.find(r => r.RoleId == roleId);
            return role ? role.RoleName : '';
        }
        return '';
    };
});

app.filter('enumDescription', function () {
    return function (value, enumName) {
        if (!enumName) {
            Q.notifyError('missing enum description in filter');
            return;
        }

        const enums = Serenity.EnumTypeRegistry.get(`AllForward.SystemEnums.${enumName}`);
        return Serenity.EnumFormatter.format(enums, enums[value]);
    };
});

app.filter('serviceIcon', function () {
    return function (id) {
        const services = Q.getLookup('ServicesLookup').itemById;
        return services[id]?.Image;
    };
});

app.filter('serviceName', function () {
    return function (id) {
        const services = Q.getLookup('ServicesLookup').itemById;
        return services[id]?.Name;
    };
});

app.filter('goodName', function () {
    return function (freightoolsUnitTypeId) {
        if (!freightoolsUnitTypeId) return '';

        const unitType = Q.getLookup('UnitTypesLookup').items.find(u => u.FreightToolsUnitTypeId == freightoolsUnitTypeId);
        return unitType.Name;
    };
});

app.filter('plusOrMinus', function(){
    return function(input){
        input = input ? input : 0
        return input > 0 ? "+"+input : input
    }
})

;

